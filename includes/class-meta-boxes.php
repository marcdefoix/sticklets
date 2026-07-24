<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sticklets_Meta_Boxes {

	public static function add_meta_boxes() {
		add_meta_box(
			'sticklets_settings',
			__( 'Sticklet Settings', 'sticklets' ),
			array( __CLASS__, 'render_meta_box' ),
			'sticklet',
			'normal',
			'high'
		);
	}

	public static function render_meta_box( $post ) {
    wp_nonce_field( 'sticklets_save_meta', 'sticklets_meta_nonce' );

    $visibility_scope             = get_post_meta( $post->ID, '_sticklet_visibility_scope', true ) ?: 'all';
    $visibility_home              = get_post_meta( $post->ID, '_sticklet_visibility_home', true );
    $visibility_blog              = get_post_meta( $post->ID, '_sticklet_visibility_blog', true );
    $visibility_search            = get_post_meta( $post->ID, '_sticklet_visibility_search', true );
    $visibility_404               = get_post_meta( $post->ID, '_sticklet_visibility_404', true );
    $visibility_ids_active        = get_post_meta( $post->ID, '_sticklet_visibility_ids_active', true );
    $visibility_ids               = get_post_meta( $post->ID, '_sticklet_visibility_ids', true );
    $trigger_mode                 = get_post_meta( $post->ID, '_sticklet_trigger_mode', true ) ?: 'load';
    $trigger_specific             = get_post_meta( $post->ID, '_sticklet_trigger_specific', true ) ?: 'scroll_px';
    $trigger_scroll_px            = intval( get_post_meta( $post->ID, '_sticklet_trigger_scroll_px', true ) );
    $trigger_scroll_element       = get_post_meta( $post->ID, '_sticklet_trigger_scroll_element', true );
    $trigger_scroll_bottom_offset = intval( get_post_meta( $post->ID, '_sticklet_trigger_scroll_bottom_offset', true ) );
    $timing_duration              = intval( get_post_meta( $post->ID, '_sticklet_timing_duration', true ) );
    $timing_delay                 = intval( get_post_meta( $post->ID, '_sticklet_timing_delay', true ) );
    $size_mode                    = get_post_meta( $post->ID, '_sticklet_size_mode', true ) ?: 'original';
    $size_width                   = get_post_meta( $post->ID, '_sticklet_size_width', true );
    $size_height                  = get_post_meta( $post->ID, '_sticklet_size_height', true );
    $position_y                   = get_post_meta( $post->ID, '_sticklet_position_y', true ) ?: 'y-bottom';
    $position_x                   = get_post_meta( $post->ID, '_sticklet_position_x', true ) ?: 'x-right';
    $position_offset_x            = intval( get_post_meta( $post->ID, '_sticklet_position_offset_x', true ) );
    $position_offset_y            = intval( get_post_meta( $post->ID, '_sticklet_position_offset_y', true ) );
    $animation_appear             = get_post_meta( $post->ID, '_sticklet_animation_appear', true );
    $animation_exit               = get_post_meta( $post->ID, '_sticklet_animation_exit', true );
    $click_url                    = get_post_meta( $post->ID, '_sticklet_click_url', true );
    ?>

    <table class="form-table">

      <tr>
        <th scope="row"><?php _e( 'Visibility', 'sticklets' ); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e( 'Visibility', 'sticklets' ); ?></legend>
            <label>
              <input type="radio" name="sticklet_visibility_scope" value="all" <?php checked( $visibility_scope, 'all' ); ?>>
              <?php _e( 'Show on the entire site', 'sticklets' ); ?>
            </label>
            <br>
            <label>
              <input type="radio" name="sticklet_visibility_scope" value="specific" <?php checked( $visibility_scope, 'specific' ); ?>>
              <?php _e( 'Limit display to specific pages', 'sticklets' ); ?>
            </label>
            <div class="sticklets-radio-target visibility-specific sticklets-conditional" style="display:<?php echo $visibility_scope === 'specific' ? 'block' : 'none'; ?>;">
              <label>
                <input type="checkbox" name="sticklet_visibility_home" value="1" <?php checked( $visibility_home, '1' ); ?>>
                <?php _e( 'Homepage', 'sticklets' ); ?>
              </label>
              <br>
              <label>
                <input type="checkbox" name="sticklet_visibility_blog" value="1" <?php checked( $visibility_blog, '1' ); ?>>
                <?php _e( 'Posts page', 'sticklets' ); ?>
              </label>
              <br>
              <label>
                <input type="checkbox" name="sticklet_visibility_search" value="1" <?php checked( $visibility_search, '1' ); ?>>
                <?php _e( 'Search results', 'sticklets' ); ?>
              </label>
              <br>
              <label>
                <input type="checkbox" name="sticklet_visibility_404" value="1" <?php checked( $visibility_404, '1' ); ?>>
                <?php _e( '404 page', 'sticklets' ); ?>
              </label>
              <br>
              <label>
                <input type="checkbox" name="sticklet_visibility_ids_active" value="1" class="sticklets-toggle-ids" <?php checked( $visibility_ids_active, '1' ); ?>>
                <?php _e( 'Specific posts/pages', 'sticklets' ); ?>
              </label>
              <div class="visibility-ids-wrap sticklets-conditional" style="display:<?php echo $visibility_ids_active ? 'block' : 'none'; ?>;">
                <label for="sticklet_visibility_ids" style="min-width: 2rem;"><?php _e( 'IDs', 'sticklets' ); ?></label>
                <input type="text" id="sticklet_visibility_ids" name="sticklet_visibility_ids" value="<?php echo esc_attr( $visibility_ids ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'e.g. 12, 45, 78', 'sticklets' ); ?>" />
                <p class="description"><?php _e( 'Enter one or more post/page IDs separated by commas.', 'sticklets' ); ?></p>
              </div>
            </div>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e( 'Trigger', 'sticklets' ); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e( 'Trigger', 'sticklets' ); ?></legend>
            <label>
              <input type="radio" name="sticklet_trigger_mode" value="load" <?php checked( $trigger_mode, 'load' ); ?>>
              <?php _e( 'Show on page load', 'sticklets' ); ?>
            </label>
            <br>
            <label>
              <input type="radio" name="sticklet_trigger_mode" value="specific" <?php checked( $trigger_mode, 'specific' ); ?>>
              <?php _e( 'Show at a specific moment', 'sticklets' ); ?>
            </label>
            <div class="trigger-specific sticklets-conditional" style="display:<?php echo $trigger_mode === 'specific' ? 'block' : 'none'; ?>;">
              <label>
                <input type="radio" name="sticklet_trigger_specific" value="scroll_px" <?php checked( $trigger_specific, 'scroll_px' ); ?>>
                <?php _e( 'After scrolling a fixed number of pixels', 'sticklets' ); ?>
              </label>
              <div class="trigger-scroll-px sticklets-conditional" style="display:<?php echo $trigger_specific === 'scroll_px' ? 'block' : 'none'; ?>;">
                <label for="sticklet_trigger_scroll_px"><?php _e( 'Scroll (px)', 'sticklets' ); ?></label>
                <input type="number" id="sticklet_trigger_scroll_px" name="sticklet_trigger_scroll_px" value="<?php echo esc_attr( $trigger_scroll_px ); ?>" class="small-text" min="0" step="1" />
              </div>
              <br>
              <label>
                <input type="radio" name="sticklet_trigger_specific" value="scroll_element" <?php checked( $trigger_specific, 'scroll_element' ); ?>>
                <?php _e( 'When a page element becomes visible', 'sticklets' ); ?>
              </label>
              <div class="trigger-scroll-element sticklets-conditional" style="display:<?php echo $trigger_specific === 'scroll_element' ? 'block' : 'none'; ?>;">
                <label for="sticklet_trigger_scroll_element"><?php _e( 'Element', 'sticklets' ); ?></label>
                <input type="text" id="sticklet_trigger_scroll_element" name="sticklet_trigger_scroll_element" value="<?php echo esc_attr( $trigger_scroll_element ); ?>" class="regular-text" placeholder="<?php esc_attr_e( '#my-section', 'sticklets' ); ?>" />
              </div>
              <br>
              <label>
                <input type="radio" name="sticklet_trigger_specific" value="scroll_bottom" <?php checked( $trigger_specific, 'scroll_bottom' ); ?>>
                <?php _e( 'When the bottom of the page is reached', 'sticklets' ); ?>
              </label>
              <div class="trigger-scroll-bottom sticklets-conditional" style="display:<?php echo $trigger_specific === 'scroll_bottom' ? 'block' : 'none'; ?>;">
                <label for="sticklet_trigger_scroll_bottom_offset"><?php _e( 'Offset (px)', 'sticklets' ); ?></label>
                <input type="number" id="sticklet_trigger_scroll_bottom_offset" name="sticklet_trigger_scroll_bottom_offset" value="<?php echo esc_attr( $trigger_scroll_bottom_offset ); ?>" class="small-text" min="0" step="1" />
              </div>
            </div>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e( 'Timing', 'sticklets' ); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e( 'Timing', 'sticklets' ); ?></legend>
            <label for="sticklet_timing_delay" style="min-width: 6rem;"><?php _e( 'Delay (ms)', 'sticklets' ); ?></label>
            <input type="number" id="sticklet_timing_delay" name="sticklet_timing_delay" value="<?php echo esc_attr( $timing_delay ); ?>" class="small-text" min="0" step="1" />
            <p class="description"><?php _e( 'Delay in milliseconds before the sticklet appears. 0 for immediate display.', 'sticklets' ); ?></p>
            <br>
            <label for="sticklet_timing_duration" style="min-width: 6rem;"><?php _e( 'Duration (ms)', 'sticklets' ); ?></label>
            <input type="number" id="sticklet_timing_duration" name="sticklet_timing_duration" value="<?php echo esc_attr( $timing_duration ); ?>" class="small-text" min="0" step="1" />
            <p class="description"><?php _e( 'Duration in milliseconds for which the sticklet remains visible. 0 for stay forever.', 'sticklets' ); ?></p>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e( 'Size', 'sticklets' ); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e( 'Size', 'sticklets' ); ?></legend>
            <label>
              <input type="radio" name="sticklet_size_mode" value="original" <?php checked( $size_mode, 'original' ); ?>>
              <?php _e( 'Original', 'sticklets' ); ?>
            </label>
            <br>
            <label>
              <input type="radio" name="sticklet_size_mode" value="cropped" <?php checked( $size_mode, 'cropped' ); ?>>
              <?php _e( 'Cropped', 'sticklets' ); ?>
            </label>
            <div class="size-cropped sticklets-conditional" style="display:<?php echo $size_mode === 'cropped' ? 'block' : 'none'; ?>;">
              <label for="sticklet_size_width" style="min-width: 5rem;"><?php _e( 'Width (px)', 'sticklets' ); ?></label>
              <input type="number" id="sticklet_size_width" name="sticklet_size_width" value="<?php echo esc_attr( $size_width ?: '' ); ?>" class="small-text" min="1" step="1" />
              <br>
              <label for="sticklet_size_height" style="min-width: 5rem;"><?php _e( 'Height (px)', 'sticklets' ); ?></label>
              <input type="number" id="sticklet_size_height" name="sticklet_size_height" value="<?php echo esc_attr( $size_height ?: '' ); ?>" class="small-text" min="1" step="1" />
            </div>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e( 'Position', 'sticklets' ); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e( 'Position', 'sticklets' ); ?></legend>
            <select id="sticklet_position_y" name="sticklet_position_y">
              <option value="y-top" <?php selected( $position_y, 'y-top' ); ?>><?php _e( 'Top', 'sticklets' ); ?></option>
              <option value="y-center" <?php selected( $position_y, 'y-center' ); ?>><?php _e( 'Center', 'sticklets' ); ?></option>
              <option value="y-bottom" <?php selected( $position_y, 'y-bottom' ); ?>><?php _e( 'Bottom', 'sticklets' ); ?></option>
            </select>
            <select id="sticklet_position_x" name="sticklet_position_x">
              <option value="x-left" <?php selected( $position_x, 'x-left' ); ?>><?php _e( 'Left', 'sticklets' ); ?></option>
              <option value="x-center" <?php selected( $position_x, 'x-center' ); ?>><?php _e( 'Center', 'sticklets' ); ?></option>
              <option value="x-right" <?php selected( $position_x, 'x-right' ); ?>><?php _e( 'Right', 'sticklets' ); ?></option>
            </select>
            <p class="description"><?php _e( 'Choose the position of the sticklet on the screen.', 'sticklets' ); ?></p>
            <br>
            <label for="sticklet_position_offset_x" style="min-width: 6rem;"><?php _e( 'Offset X (px)', 'sticklets' ); ?></label>
            <input type="number" id="sticklet_position_offset_x" name="sticklet_position_offset_x" value="<?php echo esc_attr( $position_offset_x ); ?>" class="small-text" step="1" />
            <br>
            <label for="sticklet_position_offset_y" style="min-width: 6rem;"><?php _e( 'Offset Y (px)', 'sticklets' ); ?></label>
            <input type="number" id="sticklet_position_offset_y" name="sticklet_position_offset_y" value="<?php echo esc_attr( $position_offset_y ); ?>" class="small-text" step="1" />
            <p class="description"><?php _e( 'Enter the offset in pixels. Use negative values to move in the opposite direction.', 'sticklets' ); ?></p>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><?php _e( 'Animation', 'sticklets' ); ?></th>
        <td>
          <fieldset>
            <legend class="screen-reader-text"><?php _e( 'Animation', 'sticklets' ); ?></legend>
            <label for="sticklet_animation_appear" style="min-width: 4rem;"><?php _e( 'Appear', 'sticklets' ); ?></label>
            <select id="sticklet_animation_appear" name="sticklet_animation_appear">
              <option value="none" <?php selected( $animation_appear, 'none' ); ?>><?php _e( 'None', 'sticklets' ); ?></option>
              <option value="fade-in" <?php selected( $animation_appear, 'fade-in' ); ?>><?php _e( 'Fade In', 'sticklets' ); ?></option>
              <option value="slide-up" <?php selected( $animation_appear, 'slide-up' ); ?>><?php _e( 'Slide Up', 'sticklets' ); ?></option>
              <option value="slide-down" <?php selected( $animation_appear, 'slide-down' ); ?>><?php _e( 'Slide Down', 'sticklets' ); ?></option>
              <option value="slide-left" <?php selected( $animation_appear, 'slide-left' ); ?>><?php _e( 'Slide Left', 'sticklets' ); ?></option>
              <option value="slide-right" <?php selected( $animation_appear, 'slide-right' ); ?>><?php _e( 'Slide Right', 'sticklets' ); ?></option>
            </select>
            <p class="description"><?php _e( 'Choose the animation effect when the sticklet appears.', 'sticklets' ); ?></p>
            <br>
            <label for="sticklet_animation_exit" style="min-width: 4rem;"><?php _e( 'Exit', 'sticklets' ); ?></label>
            <select id="sticklet_animation_exit" name="sticklet_animation_exit">
              <option value="none" <?php selected( $animation_exit, 'none' ); ?>><?php _e( 'None', 'sticklets' ); ?></option>
              <option value="fade-out" <?php selected( $animation_exit, 'fade-out' ); ?>><?php _e( 'Fade Out', 'sticklets' ); ?></option>
              <option value="slide-up-out" <?php selected( $animation_exit, 'slide-up-out' ); ?>><?php _e( 'Slide Up', 'sticklets' ); ?></option>
              <option value="slide-down-out" <?php selected( $animation_exit, 'slide-down-out' ); ?>><?php _e( 'Slide Down', 'sticklets' ); ?></option>
              <option value="slide-left-out" <?php selected( $animation_exit, 'slide-left-out' ); ?>><?php _e( 'Slide Left', 'sticklets' ); ?></option>
              <option value="slide-right-out" <?php selected( $animation_exit, 'slide-right-out' ); ?>><?php _e( 'Slide Right', 'sticklets' ); ?></option>
            </select>
            <p class="description"><?php _e( 'Choose the animation effect when the sticklet disappears.', 'sticklets' ); ?></p>
          </fieldset>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="sticklet_click_url"><?php _e( 'Click URL', 'sticklets' ); ?></label></th>
        <td>
          <input type="url" id="sticklet_click_url" name="sticklet_click_url" value="<?php echo esc_url( $click_url ); ?>" class="regular-text" placeholder="https://" />
          <p class="description"><?php _e( 'Optional. Enter a URL to make the sticklet clickable. Leave empty for no link.', 'sticklets' ); ?></p>
        </td>
      </tr>

    </table>
    <?php
  }

	public static function save_meta( $post_id ) {
		if ( ! isset( $_POST['sticklets_meta_nonce'] ) || ! wp_verify_nonce( $_POST['sticklets_meta_nonce'], 'sticklets_save_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( get_post_type( $post_id ) !== 'sticklet' ) {
			return;
		}

		$fields = array(
			'_sticklet_visibility_scope'             => 'sanitize_text_field',
			'_sticklet_visibility_home'              => 'intval',
			'_sticklet_visibility_blog'              => 'intval',
			'_sticklet_visibility_search'            => 'intval',
			'_sticklet_visibility_404'               => 'intval',
			'_sticklet_visibility_ids_active'        => 'intval',
			'_sticklet_visibility_ids'               => function( $value ) {
				return preg_replace( '/[^0-9,]/', '', $value );
			},
			'_sticklet_trigger_mode'                 => 'sanitize_text_field',
			'_sticklet_trigger_specific'             => 'sanitize_text_field',
			'_sticklet_trigger_scroll_px'            => 'intval',
			'_sticklet_trigger_scroll_element'       => 'sanitize_text_field',
			'_sticklet_trigger_scroll_bottom_offset' => 'intval',
			'_sticklet_timing_duration'              => 'intval',
			'_sticklet_timing_delay'                 => 'intval',
			'_sticklet_size_mode'                    => 'sanitize_text_field',
			'_sticklet_size_width'                   => 'intval',
			'_sticklet_size_height'                  => 'intval',
			'_sticklet_position_x'                   => 'sanitize_text_field',
			'_sticklet_position_y'                   => 'sanitize_text_field',
			'_sticklet_position_offset_x'            => 'intval',
			'_sticklet_position_offset_y'            => 'intval',
			'_sticklet_animation_appear'             => 'sanitize_text_field',
			'_sticklet_animation_exit'               => 'sanitize_text_field',
			'_sticklet_click_url'                    => 'esc_url_raw',
		);

		foreach ( $fields as $meta_key => $sanitize ) {
			$form_name = ltrim( $meta_key, '_' );
			if ( isset( $_POST[ $form_name ] ) ) {
				$value = call_user_func( $sanitize, $_POST[ $form_name ] );
				update_post_meta( $post_id, $meta_key, $value );
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}
	}
}