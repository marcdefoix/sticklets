<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sticklets_Public {

	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
		add_action( 'wp_footer', array( $this, 'maybe_display_sticklets' ) );
	}

	public function enqueue_public_assets() {
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

	public function maybe_display_sticklets() {
		$sticklets = get_posts( array(
			'post_type'      => 'sticklet',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'     => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			),
		) );

		if ( empty( $sticklets ) ) {
			return;
		}

		foreach ( $sticklets as $sticklet ) {
			if ( ! $this->is_sticklet_visible( $sticklet ) ) {
				continue;
			}

			$this->render_sticklet( $sticklet );
		}
	}

	private function is_sticklet_visible( $sticklet ) {
		$visibility_scope      = get_post_meta( $sticklet->ID, '_sticklet_visibility_scope', true );
		$visibility_home       = get_post_meta( $sticklet->ID, '_sticklet_visibility_home', true );
		$visibility_blog       = get_post_meta( $sticklet->ID, '_sticklet_visibility_blog', true );
		$visibility_search     = get_post_meta( $sticklet->ID, '_sticklet_visibility_search', true );
		$visibility_404        = get_post_meta( $sticklet->ID, '_sticklet_visibility_404', true );
		$visibility_ids_active = get_post_meta( $sticklet->ID, '_sticklet_visibility_ids_active', true );
		$visibility_ids        = get_post_meta( $sticklet->ID, '_sticklet_visibility_ids', true );

		if ( 'all' === $visibility_scope ) {
			return true;
		}

		if ( 'specific' !== $visibility_scope ) {
			return false;
		}

		if ( $visibility_home && ( is_front_page() || is_home() ) ) {
			return true;
		}

		if ( $visibility_blog && is_home() && ! is_front_page() ) {
			return true;
		}

		if ( $visibility_search && is_search() ) {
			return true;
		}

		if ( $visibility_404 && is_404() ) {
			return true;
		}

		if ( $visibility_ids_active && ! empty( $visibility_ids ) ) {
			$current_id  = get_queried_object_id();
			$allowed_ids = array_map( 'trim', explode( ',', $visibility_ids ) );
			$allowed_ids = array_map( 'intval', $allowed_ids );
			$allowed_ids = array_filter( $allowed_ids, function( $id ) {
				return $id > 0;
			} );

			if ( $current_id && in_array( $current_id, $allowed_ids ) ) {
				return true;
			}
		}

		return false;
	}

	private function render_sticklet( $sticklet ) {
    $image_id = get_post_thumbnail_id( $sticklet->ID );
    if ( ! $image_id ) {
      return;
    }

    $image_url = wp_get_attachment_url( $image_id );
    if ( ! $image_url ) {
      return;
    }

    $trigger_mode                 = get_post_meta( $sticklet->ID, '_sticklet_trigger_mode', true ) ?: 'load';
    $trigger_specific             = get_post_meta( $sticklet->ID, '_sticklet_trigger_specific', true ) ?: 'scroll_px';
    $trigger_scroll_px            = intval( get_post_meta( $sticklet->ID, '_sticklet_trigger_scroll_px', true ) );
    $trigger_scroll_element       = get_post_meta( $sticklet->ID, '_sticklet_trigger_scroll_element', true );
    $trigger_scroll_bottom_offset = intval( get_post_meta( $sticklet->ID, '_sticklet_trigger_scroll_bottom_offset', true ) );
    $timing_duration              = intval( get_post_meta( $sticklet->ID, '_sticklet_timing_duration', true ) );
    $timing_delay                 = intval( get_post_meta( $sticklet->ID, '_sticklet_timing_delay', true ) );
    $size_mode                    = get_post_meta( $sticklet->ID, '_sticklet_size_mode', true ) ?: 'original';
    $size_width                   = intval( get_post_meta( $sticklet->ID, '_sticklet_size_width', true ) );
    $size_height                  = intval( get_post_meta( $sticklet->ID, '_sticklet_size_height', true ) );
    $position_y                   = get_post_meta( $sticklet->ID, '_sticklet_position_y', true ) ?: 'y-bottom';
    $position_x                   = get_post_meta( $sticklet->ID, '_sticklet_position_x', true ) ?: 'x-right';
    $position_offset_x            = intval( get_post_meta( $sticklet->ID, '_sticklet_position_offset_x', true ) );
    $position_offset_y            = intval( get_post_meta( $sticklet->ID, '_sticklet_position_offset_y', true ) );
    $animation_appear             = get_post_meta( $sticklet->ID, '_sticklet_animation_appear', true ) ?: 'none';
    $animation_exit               = get_post_meta( $sticklet->ID, '_sticklet_animation_exit', true ) ?: 'none';
    $click_url                    = get_post_meta( $sticklet->ID, '_sticklet_click_url', true );

    $classes     = array( 'sticklet' );
    $style_parts = array();

    if ( 'cropped' === $size_mode && $size_width > 0 && $size_height > 0 ) {
      $classes[]     = 'sticklet--cropped';
      $style_parts[] = 'width: ' . $size_width . 'px';
      $style_parts[] = 'height: ' . $size_height . 'px';
    }

    if ( 'y-top' === $position_y ) {
      $style_parts[] = 'top: ' . $position_offset_y . 'px';
    } elseif ( 'y-bottom' === $position_y ) {
      $style_parts[] = 'bottom: ' . $position_offset_y . 'px';
    }

    if ( 'x-left' === $position_x ) {
      $style_parts[] = 'left: ' . $position_offset_x . 'px';
    } elseif ( 'x-right' === $position_x ) {
      $style_parts[] = 'right: ' . $position_offset_x . 'px';
    }

    $style_attr = '';
    if ( ! empty( $style_parts ) ) {
      $style_attr = ' style="' . esc_attr( implode( '; ', $style_parts ) ) . '"';
    }

    $data_attrs  = ' data-sticklet-id="' . esc_attr( $sticklet->ID ) . '"';
    $data_attrs .= ' data-trigger="' . esc_attr( $trigger_mode ) . '"';

    if ( 'load' === $trigger_mode ) {
      $classes[] = 'sticklet--hidden';
    } elseif ( 'specific' === $trigger_mode ) {
      $data_attrs .= ' data-trigger-specific="' . esc_attr( $trigger_specific ) . '"';
      $classes[]   = 'sticklet--hidden';

      if ( 'scroll_px' === $trigger_specific ) {
        $data_attrs .= ' data-trigger-scroll-px="' . esc_attr( $trigger_scroll_px ) . '"';
      } elseif ( 'scroll_element' === $trigger_specific ) {
        $data_attrs .= ' data-trigger-scroll-element="' . esc_attr( $trigger_scroll_element ) . '"';
      } elseif ( 'scroll_bottom' === $trigger_specific ) {
        $data_attrs .= ' data-trigger-scroll-bottom-offset="' . esc_attr( $trigger_scroll_bottom_offset ) . '"';
      }
    }

    $data_attrs .= ' data-timing-duration="' . esc_attr( $timing_duration ) . '"';
    $data_attrs .= ' data-timing-delay="' . esc_attr( $timing_delay ) . '"';
    $data_attrs .= ' data-position-y="' . esc_attr( $position_y ) . '"';
    $data_attrs .= ' data-position-x="' . esc_attr( $position_x ) . '"';
    $data_attrs .= ' data-position-offset-x="' . esc_attr( $position_offset_x ) . '"';
    $data_attrs .= ' data-position-offset-y="' . esc_attr( $position_offset_y ) . '"';
    $data_attrs .= ' data-size-mode="' . esc_attr( $size_mode ) . '"';
    $data_attrs .= ' data-size-width="' . esc_attr( $size_width ) . '"';
    $data_attrs .= ' data-size-height="' . esc_attr( $size_height ) . '"';
    $data_attrs .= ' data-animation-appear="' . esc_attr( $animation_appear ) . '"';
    $data_attrs .= ' data-animation-exit="' . esc_attr( $animation_exit ) . '"';

    echo '<div class="' . implode( ' ', $classes ) . '"' . $data_attrs . $style_attr . '>';

    if ( $click_url ) {
      echo '<a href="' . esc_url( $click_url ) . '" target="_blank" rel="noopener noreferrer">';
    }

    echo '<img src="' . esc_url( $image_url ) . '" alt="" class="sticklet__img" />';

    if ( $click_url ) {
      echo '</a>';
    }

    echo '</div>' . "\n";
  }
}
