<?php
if (! defined('ABSPATH')) {
  exit;
}

class Sticklets_Public
{

  public function init()
  {
    add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
    add_action('wp_footer', array($this, 'maybe_display_sticklets'));
  }

  public function enqueue_public_assets()
  {
    wp_enqueue_style(
      'sticklets-public',
      STICKLETS_PLUGIN_URL . 'public/css/sticklets-public.css',
      array(),
      STICKLETS_VERSION
    );

    wp_enqueue_script(
      'sticklets-public',
      STICKLETS_PLUGIN_URL . 'public/js/sticklets-public.js',
      array(),
      STICKLETS_VERSION,
      true
    );
  }

  public function maybe_display_sticklets()
  {
    $args = array(
      'post_type'      => 'sticklet',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'meta_query'     => array(
        array(
          'key'     => '_thumbnail_id',
          'compare' => 'EXISTS',
        ),
      ),
    );

    $args = apply_filters('sticklets_query_args', $args);
    $sticklets = get_posts($args);

    if (empty($sticklets)) {
      return;
    }

    foreach ($sticklets as $sticklet) {
      if (! $this->is_sticklet_visible($sticklet)) {
        continue;
      }

      $this->render_sticklet($sticklet);
    }
  }

  private function is_sticklet_visible($sticklet)
  {
    $visibility            = get_post_meta($sticklet->ID, '_sticklet_visibility', true);
    $visibility_home       = get_post_meta($sticklet->ID, '_sticklet_visibility_home', true);
    $visibility_blog       = get_post_meta($sticklet->ID, '_sticklet_visibility_blog', true);
    $visibility_search     = get_post_meta($sticklet->ID, '_sticklet_visibility_search', true);
    $visibility_404        = get_post_meta($sticklet->ID, '_sticklet_visibility_404', true);
    $visibility_ids_active = get_post_meta($sticklet->ID, '_sticklet_visibility_ids_active', true);
    $visibility_ids        = get_post_meta($sticklet->ID, '_sticklet_visibility_ids', true);

    $visible = 'all' === $visibility;

    if ('specific' === $visibility) {
      $visible = ($visibility_home && (is_front_page() || is_home()))
        || ($visibility_blog && is_home() && ! is_front_page())
        || ($visibility_search && is_search())
        || ($visibility_404 && is_404());
    }

    if ('specific' === $visibility && $visibility_ids_active && ! empty($visibility_ids)) {
      $current_id  = get_queried_object_id();
      $allowed_ids = array_map('trim', explode(',', $visibility_ids));
      $allowed_ids = array_map('intval', $allowed_ids);
      $allowed_ids = array_filter($allowed_ids, function ($id) {
        return $id > 0;
      });

      if ($current_id && in_array($current_id, $allowed_ids)) {
        $visible = true;
      }
    }

    return (bool) apply_filters('sticklets_is_visible', $visible, $sticklet);
  }

  private function render_sticklet($sticklet)
  {
    $image_id = get_post_thumbnail_id($sticklet->ID);
    if (! $image_id) {
      return;
    }

    $image_url = wp_get_attachment_url($image_id);
    if (! $image_url) {
      return;
    }

    $config = array(
      'trigger' => get_post_meta($sticklet->ID, '_sticklet_trigger', true) ?: 'load',
      'trigger_scroll_px' => get_post_meta($sticklet->ID, '_sticklet_trigger_scroll_px', true),
      'trigger_scroll_element' => get_post_meta($sticklet->ID, '_sticklet_trigger_scroll_element', true),
      'trigger_scroll_element_offset' => get_post_meta($sticklet->ID, '_sticklet_trigger_scroll_element_offset', true),
      'trigger_click_element' => get_post_meta($sticklet->ID, '_sticklet_trigger_click_element', true),
      'trigger_scroll_bottom_offset' => get_post_meta($sticklet->ID, '_sticklet_trigger_scroll_bottom_offset', true),
      'timing_duration' => get_post_meta($sticklet->ID, '_sticklet_timing_duration', true),
      'timing_delay' => get_post_meta($sticklet->ID, '_sticklet_timing_delay', true),
      'position_y' => get_post_meta($sticklet->ID, '_sticklet_position_y', true) ?: 'y-top',
      'position_x' => get_post_meta($sticklet->ID, '_sticklet_position_x', true) ?: 'x-right',
      'position_offset_x' => get_post_meta($sticklet->ID, '_sticklet_position_offset_x', true),
      'position_offset_y' => get_post_meta($sticklet->ID, '_sticklet_position_offset_y', true),
      'size_width' => get_post_meta($sticklet->ID, '_sticklet_size_width', true),
      'size_height' => get_post_meta($sticklet->ID, '_sticklet_size_height', true),
      'size_mobile' => get_post_meta($sticklet->ID, '_sticklet_size_mobile', true),
      'size_mobile_width' => get_post_meta($sticklet->ID, '_sticklet_size_mobile_width', true),
      'size_mobile_height' => get_post_meta($sticklet->ID, '_sticklet_size_mobile_height', true),
      'animation_appear' => get_post_meta($sticklet->ID, '_sticklet_animation_appear', true) ?: 'show',
      'animation_exit' => get_post_meta($sticklet->ID, '_sticklet_animation_exit', true) ?: 'hide',
      'action' => get_post_meta($sticklet->ID, '_sticklet_action', true) ?: 'none',
      'action_url' => get_post_meta($sticklet->ID, '_sticklet_action_url', true),
      'action_url_new_tab' => get_post_meta($sticklet->ID, '_sticklet_action_url_new_tab', true),
      'action_scroll_to' => get_post_meta($sticklet->ID, '_sticklet_action_scroll_to', true),
      'action_scroll_to_offset' => get_post_meta($sticklet->ID, '_sticklet_action_scroll_to_offset', true),
      'action_scroll_top_offset' => get_post_meta($sticklet->ID, '_sticklet_action_scroll_top_offset', true),
      'frequency' => get_post_meta($sticklet->ID, '_sticklet_frequency', true) ?: 'always',
      'frequency_times' => get_post_meta($sticklet->ID, '_sticklet_frequency_times', true),
    );

    $filtered_config = apply_filters('sticklets_config', $config, $sticklet);
    if (is_array($filtered_config)) {
      $config = array_merge($config, $filtered_config);
    }

    $get_integer = function ($key, $default = 0) use ($config) {
      return isset($config[$key]) && is_scalar($config[$key]) && '' !== $config[$key]
        ? intval($config[$key])
        : $default;
    };
    $get_text = function ($key) use ($config) {
      return isset($config[$key]) && is_scalar($config[$key])
        ? sanitize_text_field((string) $config[$key])
        : '';
    };

    $trigger = in_array($config['trigger'], array('load', 'scroll-px', 'scroll-element', 'click-element', 'scroll-bottom'), true) ? $config['trigger'] : 'load';
    $trigger_scroll_px = max(0, $get_integer('trigger_scroll_px'));
    $trigger_scroll_element = $get_text('trigger_scroll_element');
    $trigger_scroll_element_offset = $get_integer('trigger_scroll_element_offset');
    $trigger_click_element = $get_text('trigger_click_element');
    $trigger_scroll_bottom_offset = max(0, $get_integer('trigger_scroll_bottom_offset'));
    $timing_duration = max(0, $get_integer('timing_duration', 2500));
    $timing_delay = max(0, $get_integer('timing_delay'));
    $position_y = in_array($config['position_y'], array('y-top', 'y-center', 'y-bottom'), true) ? $config['position_y'] : 'y-top';
    $position_x = in_array($config['position_x'], array('x-left', 'x-center', 'x-right'), true) ? $config['position_x'] : 'x-right';
    $position_offset_x = $get_integer('position_offset_x');
    $position_offset_y = $get_integer('position_offset_y');
    $size_width = max(1, $get_integer('size_width', 100));
    $size_height = max(1, $get_integer('size_height', 100));
    $size_mobile = ! empty($config['size_mobile']) ? 1 : 0;
    $size_mobile_width = max(1, $get_integer('size_mobile_width', 32));
    $size_mobile_height = max(1, $get_integer('size_mobile_height', 32));
    $animation_appear = in_array($config['animation_appear'], array('show', 'fade-in', 'slide-up', 'slide-down', 'slide-left', 'slide-right'), true) ? $config['animation_appear'] : 'show';
    $animation_exit = in_array($config['animation_exit'], array('hide', 'fade-out', 'slide-up-out', 'slide-down-out', 'slide-left-out', 'slide-right-out'), true) ? $config['animation_exit'] : 'hide';
    $action = in_array($config['action'], array('none', 'url', 'scroll', 'scroll-top'), true) ? $config['action'] : 'none';
    $action_url = isset($config['action_url']) && is_scalar($config['action_url']) ? esc_url_raw((string) $config['action_url']) : '';
    $action_url_new_tab = ! empty($config['action_url_new_tab']) ? 1 : 0;
    $action_scroll_to = $get_text('action_scroll_to');
    $action_scroll_to_offset = $get_integer('action_scroll_to_offset');
    $action_scroll_top_offset = max(0, $get_integer('action_scroll_top_offset'));
    $frequency = in_array($config['frequency'], array('always', 'times'), true) ? $config['frequency'] : 'always';
    $frequency_times = max(1, $get_integer('frequency_times', 1));

    $classes     = array('sticklet', 'sticklet--hidden');
    $style_parts = array();

    if ('y-top' === $position_y) {
      $style_parts[] = 'top: ' . $position_offset_y . 'px';
    } elseif ('y-bottom' === $position_y) {
      $style_parts[] = 'bottom: ' . $position_offset_y . 'px';
    }

    if ('x-left' === $position_x) {
      $style_parts[] = 'left: ' . $position_offset_x . 'px';
    } elseif ('x-right' === $position_x) {
      $style_parts[] = 'right: ' . $position_offset_x . 'px';
    }

    if ($size_width > 0 || $size_height > 0) {
      $classes[] = 'sticklet--sized';

      if ($size_width > 0) {
        $style_parts[] = 'width: ' . $size_width . 'px';
      }

      if ($size_height > 0) {
        $style_parts[] = 'height: ' . $size_height . 'px';
      }
    }

    $style_attr = '';
    if (! empty($style_parts)) {
      $style_attr = ' style="' . esc_attr(implode('; ', $style_parts)) . '"';
    }

    $data_attrs  = ' data-sticklet-id="' . esc_attr($sticklet->ID) . '"';
    $data_attrs .= ' data-trigger="' . esc_attr($trigger) . '"';

    if ('scroll-px' === $trigger) {
      $data_attrs .= ' data-trigger-scroll-px="' . esc_attr($trigger_scroll_px) . '"';
    } elseif ('scroll-element' === $trigger) {
      $data_attrs .= ' data-trigger-scroll-element="' . esc_attr($trigger_scroll_element) . '"';
      $data_attrs .= ' data-trigger-scroll-element-offset="' . esc_attr($trigger_scroll_element_offset) . '"';
    } elseif ('click-element' === $trigger) {
      $data_attrs .= ' data-trigger-click-element="' . esc_attr($trigger_click_element) . '"';
    } elseif ('scroll-bottom' === $trigger) {
      $data_attrs .= ' data-trigger-scroll-bottom-offset="' . esc_attr($trigger_scroll_bottom_offset) . '"';
    }

    $data_attrs .= ' data-timing-duration="' . esc_attr($timing_duration) . '"';
    $data_attrs .= ' data-timing-delay="' . esc_attr($timing_delay) . '"';
    $data_attrs .= ' data-position-y="' . esc_attr($position_y) . '"';
    $data_attrs .= ' data-position-x="' . esc_attr($position_x) . '"';
    $data_attrs .= ' data-position-offset-x="' . esc_attr($position_offset_x) . '"';
    $data_attrs .= ' data-position-offset-y="' . esc_attr($position_offset_y) . '"';
    $data_attrs .= ' data-size-width="' . esc_attr($size_width) . '"';
    $data_attrs .= ' data-size-height="' . esc_attr($size_height) . '"';
    $data_attrs .= ' data-size-mobile="' . esc_attr($size_mobile) . '"';
    $data_attrs .= ' data-size-mobile-width="' . esc_attr($size_mobile_width) . '"';
    $data_attrs .= ' data-size-mobile-height="' . esc_attr($size_mobile_height) . '"';
    $data_attrs .= ' data-animation-appear="' . esc_attr($animation_appear) . '"';
    $data_attrs .= ' data-animation-exit="' . esc_attr($animation_exit) . '"';
    $data_attrs .= ' data-action="' . esc_attr($action) . '"';

    if ('url' === $action) {
      $data_attrs .= ' data-action-url="' . esc_url($action_url) . '"';
      $data_attrs .= ' data-action-new-tab="' . esc_attr($action_url_new_tab) . '"';
    } elseif ('scroll' === $action) {
      $data_attrs .= ' data-action-scroll-to="' . esc_attr($action_scroll_to) . '"';
      $data_attrs .= ' data-action-scroll-to-offset="' . esc_attr($action_scroll_to_offset) . '"';
    } elseif ('scroll-top' === $action){
      $data_attrs .= ' data-action-scroll-top-offset="' . esc_attr($action_scroll_top_offset) . '"';
    }

    $data_attrs .= ' data-frequency="' . esc_attr($frequency) . '"';
    if ('times' === $frequency) {
      $data_attrs .= ' data-frequency-times="' . esc_attr($frequency_times) . '"';
    }

    do_action('sticklets_before_render', $sticklet);

    echo '<div class="' . implode(' ', $classes) . '"' . $data_attrs . $style_attr . '>';

    if ('url' === $action && $action_url) {
      $target = $action_url_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';
      echo '<a href="' . esc_url($action_url) . '"' . $target . ' class="sticklet__link">';
    } elseif (in_array($action, array('scroll', 'scroll-top'))) {
      echo '<a href="#" class="sticklet__action">';
    }

    echo '<img src="' . esc_url($image_url) . '" alt="" class="sticklet__img" />';

    if (('url' === $action && $action_url) || in_array($action, array('scroll', 'scroll-top'), true)) {
      echo '</a>';
    }

    echo '</div>' . "\n";

    do_action('sticklets_after_render', $sticklet);
  }
}
