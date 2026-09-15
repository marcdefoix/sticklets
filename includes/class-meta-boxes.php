<?php
if (! defined('ABSPATH')) {
  exit;
}

class Sticklets_Meta_Boxes
{

  public static function add_meta_boxes()
  {
    add_meta_box(
      'sticklets_settings',
      __('Sticklet Settings', 'sticklets'),
      array(__CLASS__, 'render_meta_box'),
      'sticklet',
      'normal',
      'high'
    );
  }

  public static function render_meta_box($post)
  {
    wp_nonce_field('sticklets_save_meta', 'sticklets_meta_nonce');

    $visibility = get_post_meta($post->ID, '_sticklet_visibility', true) ?: 'all';
    $visibility_home = get_post_meta($post->ID, '_sticklet_visibility_home', true);
    $visibility_blog = get_post_meta($post->ID, '_sticklet_visibility_blog', true);
    $visibility_search = get_post_meta($post->ID, '_sticklet_visibility_search', true);
    $visibility_404 = get_post_meta($post->ID, '_sticklet_visibility_404', true);
    $visibility_ids_active = get_post_meta($post->ID, '_sticklet_visibility_ids_active', true);
    $visibility_ids = get_post_meta($post->ID, '_sticklet_visibility_ids', true);
    $trigger = get_post_meta($post->ID, '_sticklet_trigger', true) ?: 'load';
    $trigger_scroll_px = get_post_meta($post->ID, '_sticklet_trigger_scroll_px', true);
    $trigger_scroll_px = $trigger_scroll_px !== '' ? intval($trigger_scroll_px) : 0;
    $trigger_scroll_element = get_post_meta($post->ID, '_sticklet_trigger_scroll_element', true);
    $trigger_scroll_element_offset = get_post_meta($post->ID, '_sticklet_trigger_scroll_element_offset', true);
    $trigger_scroll_element_offset = $trigger_scroll_element_offset !== '' ? intval($trigger_scroll_element_offset) : 0;
    $trigger_click_element = get_post_meta($post->ID, '_sticklet_trigger_click_element', true);
    $trigger_scroll_bottom_offset = get_post_meta($post->ID, '_sticklet_trigger_scroll_bottom_offset', true);
    $trigger_scroll_bottom_offset = $trigger_scroll_bottom_offset !== '' ? intval($trigger_scroll_bottom_offset) : 0;
    $timing_duration = get_post_meta($post->ID, '_sticklet_timing_duration', true);
    $timing_duration = $timing_duration !== '' ? intval($timing_duration) : 2500;
    $timing_delay = get_post_meta($post->ID, '_sticklet_timing_delay', true);
    $timing_delay = $timing_delay !== '' ? intval($timing_delay) : 0;
    $position_y = get_post_meta($post->ID, '_sticklet_position_y', true) ?: 'y-top';
    $position_x = get_post_meta($post->ID, '_sticklet_position_x', true) ?: 'x-right';
    $position_offset_x = get_post_meta($post->ID, '_sticklet_position_offset_x', true);
    $position_offset_x = $position_offset_x !== '' ? intval($position_offset_x) : 0;
    $position_offset_y = get_post_meta($post->ID, '_sticklet_position_offset_y', true);
    $position_offset_y = $position_offset_y !== '' ? intval($position_offset_y) : 0;
    $size_width = get_post_meta($post->ID, '_sticklet_size_width', true);
    $size_width = $size_width !== '' ? intval($size_width) : 128;
    $size_height = get_post_meta($post->ID, '_sticklet_size_height', true);
    $size_height = $size_height !== '' ? intval($size_height) : 128;
    $size_mobile = get_post_meta($post->ID, '_sticklet_size_mobile', true);
    $size_mobile = $size_mobile ? 1 : 0;
    $size_mobile_width = get_post_meta($post->ID, '_sticklet_size_mobile_width', true);
    $size_mobile_width = $size_mobile_width !== '' ? intval($size_mobile_width) : 48;
    $size_mobile_height = get_post_meta($post->ID, '_sticklet_size_mobile_height', true);
    $size_mobile_height = $size_mobile_height !== '' ? intval($size_mobile_height) : 48;
    $animation_appear = get_post_meta($post->ID, '_sticklet_animation_appear', true) ?: 'none';
    $animation_exit = get_post_meta($post->ID, '_sticklet_animation_exit', true) ?: 'none';
    $action = get_post_meta($post->ID, '_sticklet_action', true) ?: 'none';
    $action_url = get_post_meta($post->ID, '_sticklet_action_url', true);
    $action_url_new_tab = get_post_meta($post->ID, '_sticklet_action_url_new_tab', true);
    $action_scroll_to = get_post_meta($post->ID, '_sticklet_action_scroll_to', true);
    $action_scroll_offset = get_post_meta($post->ID, '_sticklet_action_scroll_offset', true);
    $action_scroll_offset = $action_scroll_offset !== '' ? intval($action_scroll_offset) : 0;
    $frequency = get_post_meta($post->ID, '_sticklet_frequency', true) ?: 'always';
    $frequency_times = get_post_meta($post->ID, '_sticklet_frequency_times', true);
    $frequency_times = $frequency_times !== '' ? intval($frequency_times) : 1;
?>

    <table class="form-table">
      <tr>
        <th scope="row"><?php _e('Visibility', 'sticklets'); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e('Visibility', 'sticklets'); ?></legend>
            <label>
              <input type="radio" name="sticklet_visibility" value="all" <?php checked($visibility, 'all'); ?>>
              <?php _e('Entire site', 'sticklets'); ?>
            </label>
            <br>
            <label>
              <input type="radio" name="sticklet_visibility" value="specific" <?php checked($visibility, 'specific'); ?>>
              <?php _e('Specific pages', 'sticklets'); ?>
            </label>
            <div class="sticklets-radio-target visibility-specific sticklets-conditional" style="display:<?php echo $visibility === 'specific' ? 'block' : 'none'; ?>;">
              <label>
                <input type="checkbox" name="sticklet_visibility_home" value="1" <?php checked($visibility_home, '1'); ?>>
                <?php _e('Homepage', 'sticklets'); ?>
              </label>
              <br>
              <label>
                <input type="checkbox" name="sticklet_visibility_blog" value="1" <?php checked($visibility_blog, '1'); ?>>
                <?php _e('Posts page', 'sticklets'); ?>
              </label>
              <br>
              <label>
                <input type="checkbox" name="sticklet_visibility_search" value="1" <?php checked($visibility_search, '1'); ?>>
                <?php _e('Search results', 'sticklets'); ?>
              </label>
              <br>
              <label>
                <input type="checkbox" name="sticklet_visibility_404" value="1" <?php checked($visibility_404, '1'); ?>>
                <?php _e('404 page', 'sticklets'); ?>
              </label>
              <br>
              <label>
                <input type="checkbox" name="sticklet_visibility_ids_active" value="1" class="sticklets-toggle-ids" <?php checked($visibility_ids_active, '1'); ?>>
                <?php _e('List of IDs', 'sticklets'); ?>
              </label>
              <div class="visibility-ids-wrap sticklets-conditional" style="display:<?php echo $visibility_ids_active ? 'block' : 'none'; ?>;">
                <label for="sticklet_visibility_ids" style="min-width: 2rem;"><?php _e('IDs', 'sticklets'); ?></label>
                <input type="text" id="sticklet_visibility_ids" name="sticklet_visibility_ids" value="<?php echo esc_attr($visibility_ids); ?>" class="regular-text" placeholder="<?php esc_attr_e('e.g. 12, 45, 78', 'sticklets'); ?>" />
                <p class="description"><?php _e('Comma-separated list of post, page or custom post type IDs.', 'sticklets'); ?></p>
              </div>
            </div>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e('Trigger', 'sticklets'); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e('Trigger', 'sticklets'); ?></legend>
            <label>
              <input type="radio" name="sticklet_trigger" value="load" <?php checked($trigger, 'load'); ?>>
              <?php _e('On page load', 'sticklets'); ?>
            </label>
            <br>
            <label>
              <input type="radio" name="sticklet_trigger" value="scroll_px" <?php checked($trigger, 'scroll_px'); ?>>
              <?php _e('On scroll', 'sticklets'); ?>
            </label>
            <div class="trigger-scroll-px sticklets-conditional" style="display:<?php echo $trigger === 'scroll_px' ? 'block' : 'none'; ?>;">
              <label for="sticklet_trigger_scroll_px" style="min-width: 5rem;"><?php _e('Amount (px)', 'sticklets'); ?></label>
              <input type="number" id="sticklet_trigger_scroll_px" name="sticklet_trigger_scroll_px" value="<?php echo esc_attr($trigger_scroll_px); ?>" class="small-text" min="0" step="1" />
              <p class="description"><?php _e('Number of pixels scrolled down the page.', 'sticklets'); ?></p>
            </div>
            <br>
            <label>
              <input type="radio" name="sticklet_trigger" value="scroll_element" <?php checked($trigger, 'scroll_element'); ?>>
              <?php _e('Element appears', 'sticklets'); ?>
            </label>
            <div class="trigger-scroll-element sticklets-conditional" style="display:<?php echo $trigger === 'scroll_element' ? 'block' : 'none'; ?>;">
              <label for="sticklet_trigger_scroll_element" style="min-width: 5rem;"><?php _e('Selector', 'sticklets'); ?></label>
              <input type="text" id="sticklet_trigger_scroll_element" name="sticklet_trigger_scroll_element" value="<?php echo esc_attr($trigger_scroll_element); ?>" class="regular-text" placeholder="<?php esc_attr_e('#comments or .section', 'sticklets'); ?>" />
              <p class="description"><?php _e('CSS selector of the element to watch for (e.g. #id or .class).', 'sticklets'); ?></p>
              <br>
              <label for="sticklet_trigger_scroll_element_offset" style="min-width: 5rem;"><?php _e('Offset (px)', 'sticklets'); ?></label>
              <input type="number" id="sticklet_trigger_scroll_element_offset" name="sticklet_trigger_scroll_element_offset" value="<?php echo esc_attr($trigger_scroll_element_offset); ?>" class="small-text" min="-9999" step="1" />
              <p class="description"><?php _e('Positive shows the sticklet before the element enters view; negative waits until the element is more fully visible.', 'sticklets'); ?></p>
            </div>
            <br>
            <label>
              <input type="radio" name="sticklet_trigger" value="click_element" <?php checked($trigger, 'click_element'); ?>>
              <?php _e('Element click', 'sticklets'); ?>
            </label>
            <div class="trigger-click-element sticklets-conditional" style="display:<?php echo $trigger === 'click_element' ? 'block' : 'none'; ?>;">
              <label for="sticklet_trigger_click_element" style="min-width: 5rem;"><?php _e('Selector', 'sticklets'); ?></label>
              <input type="text" id="sticklet_trigger_click_element" name="sticklet_trigger_click_element" value="<?php echo esc_attr($trigger_click_element); ?>" class="regular-text" placeholder="<?php esc_attr_e('#button or .cta', 'sticklets'); ?>" />
              <p class="description"><?php _e('CSS selector of the element which, when clicked, will show the sticklet (e.g. #button or .cta).', 'sticklets'); ?></p>
            </div>
            <br>
            <label>
              <input type="radio" name="sticklet_trigger" value="scroll_bottom" <?php checked($trigger, 'scroll_bottom'); ?>>
              <?php _e('At page bottom', 'sticklets'); ?>
            </label>
            <div class="trigger-scroll-bottom sticklets-conditional" style="display:<?php echo $trigger === 'scroll_bottom' ? 'block' : 'none'; ?>;">
              <label for="sticklet_trigger_scroll_bottom_offset" style="min-width: 5rem;"><?php _e('Offset (px)', 'sticklets'); ?></label>
              <input type="number" id="sticklet_trigger_scroll_bottom_offset" name="sticklet_trigger_scroll_bottom_offset" value="<?php echo esc_attr($trigger_scroll_bottom_offset); ?>" class="small-text" min="0" step="1" />
              <p class="description"><?php _e('Pixels from the page bottom (e.g. 0 = exact bottom).', 'sticklets'); ?></p>
            </div>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e('Timing', 'sticklets'); ?></th>
        <td>
          <fieldset>
            <label for="sticklet_timing_duration" style="min-width: 6rem;"><?php _e('Duration (ms)', 'sticklets'); ?></label>
            <input type="number" id="sticklet_timing_duration" name="sticklet_timing_duration" value="<?php echo esc_attr($timing_duration); ?>" class="small-text" min="0" step="1" />
            <p class="description"><?php _e('How long it stays visible. 0 = stays until closed or page left.', 'sticklets'); ?></p>
            <br>
            <legend class="screen-reader-text"><?php _e('Timing', 'sticklets'); ?></legend>
            <label for="sticklet_timing_delay" style="min-width: 6rem;"><?php _e('Delay (ms)', 'sticklets'); ?></label>
            <input type="number" id="sticklet_timing_delay" name="sticklet_timing_delay" value="<?php echo esc_attr($timing_delay); ?>" class="small-text" min="0" step="1" />
            <p class="description"><?php _e('Wait time before showing. 0 = immediate.', 'sticklets'); ?></p>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e('Position', 'sticklets'); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e('Position', 'sticklets'); ?></legend>
            <label for="sticklet_position_y" style="min-width: 6rem;"><?php _e('Vertical', 'sticklets'); ?></label>
            <select id="sticklet_position_y" name="sticklet_position_y">
              <option value="y-top" <?php selected($position_y, 'y-top'); ?>><?php _e('Top', 'sticklets'); ?></option>
              <option value="y-center" <?php selected($position_y, 'y-center'); ?>><?php _e('Center', 'sticklets'); ?></option>
              <option value="y-bottom" <?php selected($position_y, 'y-bottom'); ?>><?php _e('Bottom', 'sticklets'); ?></option>
            </select>
            <p class="description"><?php _e('Vertical anchor point on the screen.', 'sticklets'); ?></p>
            <br>
            <label for="sticklet_position_x" style="min-width: 6rem;"><?php _e('Horizontal', 'sticklets'); ?></label>
            <select id="sticklet_position_x" name="sticklet_position_x">
              <option value="x-left" <?php selected($position_x, 'x-left'); ?>><?php _e('Left', 'sticklets'); ?></option>
              <option value="x-center" <?php selected($position_x, 'x-center'); ?>><?php _e('Center', 'sticklets'); ?></option>
              <option value="x-right" <?php selected($position_x, 'x-right'); ?>><?php _e('Right', 'sticklets'); ?></option>
            </select>
            <p class="description"><?php _e('Horizontal anchor point on the screen.', 'sticklets'); ?></p>
            <br>
            <label for="sticklet_position_offset_x" style="min-width: 6rem;"><?php _e('Offset X (px)', 'sticklets'); ?></label>
            <input type="number" id="sticklet_position_offset_x" name="sticklet_position_offset_x" value="<?php echo esc_attr($position_offset_x); ?>" class="small-text" step="1" />
            <p class="description"><?php _e('Horizontal offset. Negative values shift towards the opposite direction.', 'sticklets'); ?></p>
            <br>
            <label for="sticklet_position_offset_y" style="min-width: 6rem;"><?php _e('Offset Y (px)', 'sticklets'); ?></label>
            <input type="number" id="sticklet_position_offset_y" name="sticklet_position_offset_y" value="<?php echo esc_attr($position_offset_y); ?>" class="small-text" step="1" />
            <p class="description"><?php _e('Vertical offset. Negative values shift towards the opposite direction.', 'sticklets'); ?></p>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e('Size', 'sticklets'); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e('Size', 'sticklets'); ?></legend>

            <label for="sticklet_size_width" style="min-width: 5rem;"><?php _e('Width (px)', 'sticklets'); ?></label>
            <input type="number" id="sticklet_size_width" name="sticklet_size_width" value="<?php echo esc_attr($size_width); ?>" class="small-text" min="1" step="1" />
            <p class="description"><?php _e('Width on desktop and tablet screens.', 'sticklets'); ?></p>
            <br>
            <label for="sticklet_size_height" style="min-width: 5rem;"><?php _e('Height (px)', 'sticklets'); ?></label>
            <input type="number" id="sticklet_size_height" name="sticklet_size_height" value="<?php echo esc_attr($size_height); ?>" class="small-text" min="1" step="1" />
            <p class="description"><?php _e('Height on desktop and tablet screens.', 'sticklets'); ?></p>
            <br>
            <label class="sticklets-checkbox-label">
              <input type="checkbox" id="sticklet_size_mobile" name="sticklet_size_mobile" value="1" <?php checked($size_mobile, 1); ?> />
              <?php _e('Enable custom mobile width and height', 'sticklets'); ?>
            </label>
            <div class="mobile-size-fields sticklets-conditional" style="display:<?php echo $size_mobile ? 'block' : 'none'; ?>;">
              <label for="sticklet_size_mobile_width" style="min-width: 5rem; display:inline-block; margin-right: 0.5rem;"><?php _e('Mobile width', 'sticklets'); ?></label>
              <input type="number" id="sticklet_size_mobile_width" name="sticklet_size_mobile_width" value="<?php echo esc_attr($size_mobile_width); ?>" class="small-text" min="1" step="1" />
              <label for="sticklet_size_mobile_height" style="min-width: 5rem; display:inline-block; margin: 0 0 .5rem 1rem;"><?php _e('Mobile height', 'sticklets'); ?></label>
              <input type="number" id="sticklet_size_mobile_height" name="sticklet_size_mobile_height" value="<?php echo esc_attr($size_mobile_height); ?>" class="small-text" min="1" step="1" />
              <p class="description"><?php _e('Set explicit width and height for small screens. Values are in pixels.', 'sticklets'); ?></p>
            </div>

          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e('Animation', 'sticklets'); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e('Animation', 'sticklets'); ?></legend>
            <label for="sticklet_animation_appear" style="min-width: 4rem;"><?php _e('Entrance', 'sticklets'); ?></label>
            <select id="sticklet_animation_appear" name="sticklet_animation_appear">
              <option value="none" <?php selected($animation_appear, 'none'); ?>><?php _e('None', 'sticklets'); ?></option>
              <option value="fade-in" <?php selected($animation_appear, 'fade-in'); ?>><?php _e('Fade In', 'sticklets'); ?></option>
              <option value="slide-up" <?php selected($animation_appear, 'slide-up'); ?>><?php _e('Slide Up', 'sticklets'); ?></option>
              <option value="slide-down" <?php selected($animation_appear, 'slide-down'); ?>><?php _e('Slide Down', 'sticklets'); ?></option>
              <option value="slide-left" <?php selected($animation_appear, 'slide-left'); ?>><?php _e('Slide Left', 'sticklets'); ?></option>
              <option value="slide-right" <?php selected($animation_appear, 'slide-right'); ?>><?php _e('Slide Right', 'sticklets'); ?></option>
            </select>
            <p class="description"><?php _e('How the sticklet animates in.', 'sticklets'); ?></p>
            <br>
            <label for="sticklet_animation_exit" style="min-width: 4rem;"><?php _e('Exit', 'sticklets'); ?></label>
            <select id="sticklet_animation_exit" name="sticklet_animation_exit">
              <option value="none" <?php selected($animation_exit, 'none'); ?>><?php _e('None', 'sticklets'); ?></option>
              <option value="fade-out" <?php selected($animation_exit, 'fade-out'); ?>><?php _e('Fade Out', 'sticklets'); ?></option>
              <option value="slide-up-out" <?php selected($animation_exit, 'slide-up-out'); ?>><?php _e('Slide Up', 'sticklets'); ?></option>
              <option value="slide-down-out" <?php selected($animation_exit, 'slide-down-out'); ?>><?php _e('Slide Down', 'sticklets'); ?></option>
              <option value="slide-left-out" <?php selected($animation_exit, 'slide-left-out'); ?>><?php _e('Slide Left', 'sticklets'); ?></option>
              <option value="slide-right-out" <?php selected($animation_exit, 'slide-right-out'); ?>><?php _e('Slide Right', 'sticklets'); ?></option>
            </select>
            <p class="description"><?php _e('How the sticklet animates out.', 'sticklets'); ?></p>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e('Action', 'sticklets'); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e('Action', 'sticklets'); ?></legend>
            <label>
              <input type="radio" name="sticklet_action" value="none" <?php checked($action, 'none'); ?>>
              <?php _e('None', 'sticklets'); ?>
            </label>
            <br>
            <label>
              <input type="radio" name="sticklet_action" value="url" <?php checked($action, 'url'); ?>>
              <?php _e('Open URL', 'sticklets'); ?>
            </label>
            <div class="action-url sticklets-conditional" style="display:<?php echo $action === 'url' ? 'block' : 'none'; ?>;">
              <label for="sticklet_action_url" style="min-width: 2rem;"><?php _e('URL', 'sticklets'); ?></label>
              <input type="url" id="sticklet_action_url" name="sticklet_action_url" value="<?php echo esc_url($action_url); ?>" class="regular-text" placeholder="<?php esc_attr_e('https://example.com', 'sticklets'); ?>" />
              <br>
              <label>
                <input type="checkbox" name="sticklet_action_url_new_tab" value="1" <?php checked($action_url_new_tab, '1'); ?>>
                <?php _e('Open in new tab', 'sticklets'); ?>
              </label>
            </div>
            <br>
            <label>
              <input type="radio" name="sticklet_action" value="scroll" <?php checked($action, 'scroll'); ?>>
              <?php _e('Scroll to element', 'sticklets'); ?>
            </label>
            <div class="action-scroll sticklets-conditional" style="display:<?php echo $action === 'scroll' ? 'block' : 'none'; ?>;">
              <label for="sticklet_action_scroll_to" style="min-width: 5rem;"><?php _e('Selector', 'sticklets'); ?></label>
              <input type="text" id="sticklet_action_scroll_to" name="sticklet_action_scroll_to" value="<?php echo esc_attr($action_scroll_to); ?>" class="regular-text" placeholder="<?php esc_attr_e('#contact', 'sticklets'); ?>" />
              <p class="description"><?php _e('CSS selector of the target element.', 'sticklets'); ?></p>
              <br>
              <label for="sticklet_action_scroll_offset" style="min-width: 5rem;"><?php _e('Offset (px)', 'sticklets'); ?></label>
              <input type="number" id="sticklet_action_scroll_offset" name="sticklet_action_scroll_offset" value="<?php echo esc_attr($action_scroll_offset); ?>" class="small-text" min="-9999" step="1" />
              <p class="description"><?php _e('Positive values leave space above the target; negative values scroll past its top.', 'sticklets'); ?></p>
            </div>
            <br>
            <label>
              <input type="radio" name="sticklet_action" value="scrolltop" <?php checked($action, 'scrolltop'); ?>>
              <?php _e('Scroll to top', 'sticklets'); ?>
            </label>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e('Frequency', 'sticklets'); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e('Frequency', 'sticklets'); ?></legend>
            <label>
              <input type="radio" name="sticklet_frequency" value="always" <?php checked($frequency, 'always'); ?> />
              <?php _e('Always', 'sticklets'); ?>
            </label>
            <br>
            <label>
              <input type="radio" name="sticklet_frequency" value="times" <?php checked($frequency, 'times'); ?> />
              <?php _e('Limited times', 'sticklets'); ?>
            </label>
            <div class="frequency-times sticklets-conditional" style="display:<?php echo $frequency === 'times' ? 'block' : 'none'; ?>;">
              <label for="sticklet_frequency_times" style="min-width: 5rem;"><?php _e('Times', 'sticklets'); ?></label>
              <input type="number" id="sticklet_frequency_times" name="sticklet_frequency_times" value="<?php echo esc_attr($frequency_times); ?>" class="small-text" min="1" step="1" />
              <p class="description"><?php _e('Maximum number of times this sticklet appears in this browser.', 'sticklets'); ?></p>
            </div>
          </fieldset>
        </td>
      </tr>
    </table>
<?php
  }

  public static function save_meta($post_id)
  {
    if (! isset($_POST['sticklets_meta_nonce']) || ! wp_verify_nonce($_POST['sticklets_meta_nonce'], 'sticklets_save_meta')) {
      return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return;
    }
    if (! current_user_can('edit_post', $post_id)) {
      return;
    }
    if (wp_is_post_revision($post_id)) {
      return;
    }
    if (get_post_type($post_id) !== 'sticklet') {
      return;
    }

    $fields = array(
      '_sticklet_visibility' => 'sanitize_text_field',
      '_sticklet_visibility_home' => 'intval',
      '_sticklet_visibility_blog' => 'intval',
      '_sticklet_visibility_search' => 'intval',
      '_sticklet_visibility_404' => 'intval',
      '_sticklet_visibility_ids_active' => 'intval',
      '_sticklet_visibility_ids' => function ($value) {
        return preg_replace('/[^0-9,]/', '', $value);
      },
      '_sticklet_trigger' => 'sanitize_text_field',
      '_sticklet_trigger_scroll_px' => 'intval',
      '_sticklet_trigger_scroll_element' => 'sanitize_text_field',
      '_sticklet_trigger_scroll_element_offset' => 'intval',
      '_sticklet_trigger_click_element' => 'sanitize_text_field',
      '_sticklet_trigger_scroll_bottom_offset' => 'intval',
      '_sticklet_timing_duration' => 'intval',
      '_sticklet_timing_delay' => 'intval',
      '_sticklet_position_x' => function ($value) {
        $value = sanitize_text_field($value);
        return in_array($value, array('x-left', 'x-center', 'x-right'), true) ? $value : 'x-right';
      },
      '_sticklet_position_y' => function ($value) {
        $value = sanitize_text_field($value);
        return in_array($value, array('y-top', 'y-center', 'y-bottom'), true) ? $value : 'y-top';
      },
      '_sticklet_position_offset_x' => 'intval',
      '_sticklet_position_offset_y' => 'intval',
      '_sticklet_size_width' => 'intval',
      '_sticklet_size_height' => 'intval',
      '_sticklet_size_mobile_width' => 'intval',
      '_sticklet_size_mobile_height' => 'intval', 
      '_sticklet_size_mobile' => 'intval',
      '_sticklet_animation_appear' => function ($value) {
        $value = sanitize_text_field($value);
        return in_array($value, array('none', 'fade-in', 'slide-up', 'slide-down', 'slide-left', 'slide-right'), true) ? $value : 'none';
      },
      '_sticklet_animation_exit' => function ($value) {
        $value = sanitize_text_field($value);
        return in_array($value, array('none', 'fade-out', 'slide-up-out', 'slide-down-out', 'slide-left-out', 'slide-right-out'), true) ? $value : 'none';
      },
      '_sticklet_action' => function ($value) {
        $value = sanitize_text_field($value);
        return in_array($value, array('none', 'url', 'scroll', 'scrolltop'), true) ? $value : 'none';
      },
      '_sticklet_action_url' => 'esc_url_raw',
      '_sticklet_action_url_new_tab' => 'intval',
      '_sticklet_action_scroll_to' => 'sanitize_text_field',
      '_sticklet_action_scroll_offset' => 'intval',
      '_sticklet_frequency' => function ($value) {
        $value = sanitize_text_field($value);
        return in_array($value, array('always', 'times'), true) ? $value : 'always';
      },
      '_sticklet_frequency_times' => function ($value) {
        return max(1, intval($value));
      },
    );

    foreach ($fields as $meta_key => $sanitize) {
      $form_name = ltrim($meta_key, '_');
      if (isset($_POST[$form_name])) {
        $value = call_user_func($sanitize, $_POST[$form_name]);
        update_post_meta($post_id, $meta_key, $value);
      } else {
        delete_post_meta($post_id, $meta_key);
      }
    }
  }
}
