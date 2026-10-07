<?php
if (! defined('ABSPATH')) {
  exit;
}

class Sticklets_Admin
{
  public function init()
  {
    add_action('init', array('Sticklets_Post_Type', 'register'));
    add_action('add_meta_boxes', array('Sticklets_Meta_Boxes', 'add_meta_boxes'));
    add_action('do_meta_boxes', array($this, 'reposition_featured_image_metabox'));
    add_action('save_post', array('Sticklets_Meta_Boxes', 'save_meta'));
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    add_filter('manage_sticklet_posts_columns', array($this, 'add_sticklet_columns'));
    add_action('manage_sticklet_posts_custom_column', array($this, 'render_sticklet_columns'), 10, 2);
  }

  public function reposition_featured_image_metabox()
  {
    remove_meta_box('postimagediv', 'sticklet', 'side');
    add_meta_box(
      'postimagediv',
      __('Sticklet Image', 'sticklets'),
      'post_thumbnail_meta_box',
      'sticklet',
      'normal',
      'high'
    );
  }

  public function enqueue_admin_assets($hook)
  {
    global $post_type;

    if ('sticklet' !== $post_type) {
      return;
    }

    wp_enqueue_style(
      'sticklets-admin',
      STICKLETS_PLUGIN_URL . 'admin/css/sticklets-admin.css',
      array(),
      STICKLETS_VERSION
    );

    wp_enqueue_script(
      'sticklets-admin',
      STICKLETS_PLUGIN_URL . 'admin/js/sticklets-admin.js',
      array('jquery'),
      STICKLETS_VERSION,
      true
    );
  }

  public function add_sticklet_columns($columns)
  {
    $new_columns = array();

    foreach ($columns as $key => $value) {
      $new_columns[$key] = $value;

      if ('title' === $key) {
        $new_columns['thumbnail']  = __('Image', 'sticklets');
        $new_columns['visibility'] = __('Visibility', 'sticklets');
        $new_columns['trigger']    = __('Trigger', 'sticklets');
        $new_columns['timing']     = __('Timing', 'sticklets');
        $new_columns['position']   = __('Position', 'sticklets');
        $new_columns['size']       = __('Size', 'sticklets');
        $new_columns['animation']  = __('Animation', 'sticklets');
        $new_columns['action']     = __('Action', 'sticklets');
        $new_columns['frequency']  = __('Frequency', 'sticklets');
      }
    }

    return $new_columns;
  }

  public function render_sticklet_columns($column, $post_id)
  {
    switch ($column) {
      case 'thumbnail':
        $image_id = get_post_thumbnail_id($post_id);
        if ($image_id) {
          echo wp_get_attachment_image($image_id, array(48, 48), false, array(
            'style' => 'max-width:48px; height:auto; display:block; border-radius:2px;',
          ));
        } else {
          echo '<span style="color:#a7aaad;">—</span>';
        }
        break;

      case 'visibility':
        $visibility = get_post_meta($post_id, '_sticklet_visibility', true) ?: 'all';
        $home       = get_post_meta($post_id, '_sticklet_visibility_home', true);
        $blog       = get_post_meta($post_id, '_sticklet_visibility_blog', true);
        $search     = get_post_meta($post_id, '_sticklet_visibility_search', true);
        $not_found  = get_post_meta($post_id, '_sticklet_visibility_404', true);
        $ids_active = get_post_meta($post_id, '_sticklet_visibility_ids_active', true);
        $ids        = get_post_meta($post_id, '_sticklet_visibility_ids', true);

        if ('all' === $visibility) {
          _e('Entire site', 'sticklets');
        } elseif ('specific' === $visibility) {
          $parts = array();
          if ($home) {
            $parts[] = __('Home', 'sticklets');
          }
          if ($blog) {
            $parts[] = __('Posts', 'sticklets');
          }
          if ($search) {
            $parts[] = __('Search', 'sticklets');
          }
          if ($not_found) {
            $parts[] = __('404', 'sticklets');
          }
          if ($ids_active && ! empty($ids)) {
            $parts[] = esc_html($ids);
          }
          if (empty($parts)) {
            echo '—';
          } else {
            echo implode(' + ', $parts);
          }
        } else {
          echo '—';
        }
        break;

      case 'trigger':
        $mode           = get_post_meta($post_id, '_sticklet_trigger', true) ?: 'load';
        $scroll_px      = max(0, intval(get_post_meta($post_id, '_sticklet_trigger_scroll_px', true)));
        $element        = get_post_meta($post_id, '_sticklet_trigger_scroll_element', true);
        $element_offset = intval(get_post_meta($post_id, '_sticklet_trigger_scroll_element_offset', true));
        $click_element  = get_post_meta($post_id, '_sticklet_trigger_click_element', true);
        $bottom         = max(0, intval(get_post_meta($post_id, '_sticklet_trigger_scroll_bottom_offset', true)));

        switch ($mode) {
          case 'load':
            _e('On page load', 'sticklets');
            break;

          case 'scroll-px':
            echo __('On scroll', 'sticklets');
            echo ' — ' . esc_html($scroll_px) . 'px';
            break;

          case 'scroll-element':
            _e('Element appears', 'sticklets');
            if ($element) {
              echo ' — <code>' . esc_html($element) . '</code>';
            }
            if ($element_offset !== 0) {
              echo ' (' . ($element_offset > 0 ? '+' : '') . esc_html($element_offset) . 'px)';
            }
            break;

          case 'click-element':
            _e('Element click', 'sticklets');
            if ($click_element) {
              echo ' — <code>' . esc_html($click_element) . '</code>';
            }
            break;

          case 'scroll-bottom':
            _e('At page bottom', 'sticklets');
            if ($bottom > 0) {
              echo ' — ' . esc_html($bottom) . 'px';
            }
            break;

          default:
            echo '—';
        }
        break;

      case 'timing':
        $duration = max(0, intval(get_post_meta($post_id, '_sticklet_timing_duration', true)));
        $delay    = max(0, intval(get_post_meta($post_id, '_sticklet_timing_delay', true)));
        $parts    = array();

        if ($delay > 0) {
          $parts[] = sprintf(__('%dms delay', 'sticklets'), $delay);
        }
        if ($duration > 0) {
          $parts[] = sprintf(__('%dms duration', 'sticklets'), $duration);
        }

        if (empty($parts)) {
          echo '—';
        } else {
          echo esc_html(implode(' | ', $parts));
        }
        break;

      case 'position':
        $position_y = get_post_meta($post_id, '_sticklet_position_y', true) ?: 'y-top';
        $position_y = in_array($position_y, array('y-top', 'y-center', 'y-bottom'), true) ? $position_y : 'y-top';
        $position_x = get_post_meta($post_id, '_sticklet_position_x', true) ?: 'x-right';
        $position_x = in_array($position_x, array('x-left', 'x-center', 'x-right'), true) ? $position_x : 'x-right';
        $offset_x   = intval(get_post_meta($post_id, '_sticklet_position_offset_x', true));
        $offset_y   = intval(get_post_meta($post_id, '_sticklet_position_offset_y', true));

        $v_labels = array(
          'y-top'    => __('Top', 'sticklets'),
          'y-center' => __('Center', 'sticklets'),
          'y-bottom' => __('Bottom', 'sticklets'),
        );
        $h_labels = array(
          'x-left'   => __('Left', 'sticklets'),
          'x-center' => __('Center', 'sticklets'),
          'x-right'  => __('Right', 'sticklets'),
        );

        $parts   = array();
        $parts[] = $v_labels[$position_y] . ' / ' . $h_labels[$position_x];

        if ($offset_x !== 0 || $offset_y !== 0) {
          $parts[] = sprintf('X:%d Y:%d', $offset_x, $offset_y);
        }

        echo esc_html(implode(' — ', $parts));
        break;

      case 'size':
        $width         = max(1, intval(get_post_meta($post_id, '_sticklet_size_width', true)));
        $height        = max(1, intval(get_post_meta($post_id, '_sticklet_size_height', true)));
        $mobile_active = intval(get_post_meta($post_id, '_sticklet_size_mobile', true));
        $mobile_width  = max(1, intval(get_post_meta($post_id, '_sticklet_size_mobile_width', true)));
        $mobile_height = max(1, intval(get_post_meta($post_id, '_sticklet_size_mobile_height', true)));

        echo esc_html($width . '×' . $height . 'px');

        if ($mobile_active) {
          echo '<br><small>' . esc_html($mobile_width . '×' . $mobile_height . 'px') . '</small>';
        }
        break;

      case 'animation':
        $appear = get_post_meta($post_id, '_sticklet_animation_appear', true) ?: 'show';
        $exit   = get_post_meta($post_id, '_sticklet_animation_exit', true) ?: 'hide';
        $parts  = array();

        if ('none' !== $appear) {
          $parts[] = __('In', 'sticklets') . ': ' . ucfirst(str_replace('-', ' ', $appear));
        }
        if ('none' !== $exit) {
          $parts[] = __('Out', 'sticklets') . ': ' . ucfirst(str_replace('-', ' ', $exit));
        }

        if (empty($parts)) {
          echo '—';
        } else {
          echo esc_html(implode(' | ', $parts));
        }
        break;

      case 'action':
        $action               = get_post_meta($post_id, '_sticklet_action', true) ?: 'none';
        $action               = in_array($action, array('none', 'url', 'scroll', 'scroll-top'), true) ? $action : 'none';
        $action_url           = get_post_meta($post_id, '_sticklet_action_url', true);
        $action_url_new_tab   = get_post_meta($post_id, '_sticklet_action_url_new_tab', true);
        $action_scroll_to     = get_post_meta($post_id, '_sticklet_action_scroll_to', true);
        $action_scroll_to_offset = intval(get_post_meta($post_id, '_sticklet_action_scroll_to_offset', true));
        $action_scroll_top_offset = intval(get_post_meta($post_id, '_sticklet_action_scroll_top_offset', true));

        switch ($action) {
          case 'none':
            echo '—';
            break;

          case 'url':
            if ($action_url) {
              echo esc_html(wp_parse_url($action_url, PHP_URL_HOST) ?: $action_url);
              if ($action_url_new_tab) {
                echo ' ' . esc_html__('(new tab)', 'sticklets');
              }
            } else {
              echo '—';
            }
            break;

          case 'scroll':
            _e('Scroll to', 'sticklets');
            if ($action_scroll_to) {
              echo ' <code>' . esc_html($action_scroll_to) . '</code>';
            }
            if ($action_scroll_to_offset !== 0) {
              echo ' (' . ($action_scroll_to_offset > 0 ? '+' : '') . esc_html($action_scroll_to_offset) . 'px)';
            }
            break;

          case 'scroll-top':
            _e('Scroll to top', 'sticklets');
            if ($action_scroll_top_offset !== 0) {
              echo ' (' . ($action_scroll_top_offset > 0 ? '+' : '') . esc_html($action_scroll_top_offset) . 'px)';
            }
            break;

          default:
            echo '—';
        }
        break;

      case 'frequency':
        $frequency = get_post_meta($post_id, '_sticklet_frequency', true) ?: 'always';
        $frequency = in_array($frequency, array('always', 'times'), true) ? $frequency : 'always';
        $times     = max(1, intval(get_post_meta($post_id, '_sticklet_frequency_times', true)));

        switch ($frequency) {
          case 'always':
            _e('Always', 'sticklets');
            break;

          case 'times':
            echo sprintf(__('%d times', 'sticklets'), $times);
            break;

          default:
            echo '—';
        }
        break;
    }
  }
}
