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
		$visibility      = get_post_meta( $sticklet->ID, '_sticklet_visibility', true );
		$visibility_home       = get_post_meta( $sticklet->ID, '_sticklet_visibility_home', true );
		$visibility_blog       = get_post_meta( $sticklet->ID, '_sticklet_visibility_blog', true );
		$visibility_search     = get_post_meta( $sticklet->ID, '_sticklet_visibility_search', true );
		$visibility_404        = get_post_meta( $sticklet->ID, '_sticklet_visibility_404', true );
		$visibility_ids_active = get_post_meta( $sticklet->ID, '_sticklet_visibility_ids_active', true );
		$visibility_ids        = get_post_meta( $sticklet->ID, '_sticklet_visibility_ids', true );

		if ( 'all' === $visibility ) {
			return true;
		}

		if ( 'specific' !== $visibility ) {
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

		$trigger                 = get_post_meta( $sticklet->ID, '_sticklet_trigger', true ) ?: 'load';
		$trigger_specific             = get_post_meta( $sticklet->ID, '_sticklet_trigger_specific', true ) ?: 'scroll_px';
		$trigger_scroll_px            = intval( get_post_meta( $sticklet->ID, '_sticklet_trigger_scroll_px', true ) );
		$trigger_scroll_element       = get_post_meta( $sticklet->ID, '_sticklet_trigger_scroll_element', true );
		$trigger_scroll_bottom_offset = intval( get_post_meta( $sticklet->ID, '_sticklet_trigger_scroll_bottom_offset', true ) );
		$timing_duration              = intval( get_post_meta( $sticklet->ID, '_sticklet_timing_duration', true ) );
		$timing_delay                 = intval( get_post_meta( $sticklet->ID, '_sticklet_timing_delay', true ) );
		$size_width                   = intval( get_post_meta( $sticklet->ID, '_sticklet_size_width', true ) );
    $size_height                  = intval( get_post_meta( $sticklet->ID, '_sticklet_size_height', true ) );
    $size_mobile_width            = intval( get_post_meta( $sticklet->ID, '_sticklet_size_mobile_width', true ) );
    $size_mobile_height           = intval( get_post_meta( $sticklet->ID, '_sticklet_size_mobile_height', true ) );
		$position_y                   = get_post_meta( $sticklet->ID, '_sticklet_position_y', true ) ?: 'y-bottom';
		$position_x                   = get_post_meta( $sticklet->ID, '_sticklet_position_x', true ) ?: 'x-right';
		$position_offset_x            = intval( get_post_meta( $sticklet->ID, '_sticklet_position_offset_x', true ) );
		$position_offset_y            = intval( get_post_meta( $sticklet->ID, '_sticklet_position_offset_y', true ) );
		$animation_appear             = get_post_meta( $sticklet->ID, '_sticklet_animation_appear', true ) ?: 'none';
		$animation_exit               = get_post_meta( $sticklet->ID, '_sticklet_animation_exit', true ) ?: 'none';
		$action                  = get_post_meta( $sticklet->ID, '_sticklet_action', true ) ?: 'none';
		$action_url                   = get_post_meta( $sticklet->ID, '_sticklet_action_url', true );
		$action_url_new_tab               = get_post_meta( $sticklet->ID, '_sticklet_action_url_new_tab', true );
		$action_scroll_to             = get_post_meta( $sticklet->ID, '_sticklet_action_scroll_to', true );
		$action_scroll_offset         = intval( get_post_meta( $sticklet->ID, '_sticklet_action_scroll_offset', true ) );

		$classes     = array( 'sticklet' );
		$style_parts = array();

		if ( $size_width > 0 || $size_height > 0 ) {
      $classes[] = 'sticklet--sized';

      if ( $size_width > 0 ) {
        $style_parts[] = 'width: ' . $size_width . 'px';
      }

      if ( $size_height > 0 ) {
        $style_parts[] = 'height: ' . $size_height . 'px';
      }
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
		$data_attrs .= ' data-trigger="' . esc_attr( $trigger ) . '"';

    if ( 'load' !== $trigger ) {
        $classes[] = 'sticklet--hidden';
    }

    if ( 'scroll_px' === $trigger ) {
        $data_attrs .= ' data-trigger-scroll-px="' . esc_attr( $trigger_scroll_px ) . '"';
    } elseif ( 'scroll_element' === $trigger ) {
        $data_attrs .= ' data-trigger-scroll-element="' . esc_attr( $trigger_scroll_element ) . '"';
    } elseif ( 'scroll_bottom' === $trigger ) {
        $data_attrs .= ' data-trigger-scroll-bottom-offset="' . esc_attr( $trigger_scroll_bottom_offset ) . '"';
    }

		$data_attrs .= ' data-timing-duration="' . esc_attr( $timing_duration ) . '"';
		$data_attrs .= ' data-timing-delay="' . esc_attr( $timing_delay ) . '"';
		$data_attrs .= ' data-position-y="' . esc_attr( $position_y ) . '"';
		$data_attrs .= ' data-position-x="' . esc_attr( $position_x ) . '"';
		$data_attrs .= ' data-position-offset-x="' . esc_attr( $position_offset_x ) . '"';
		$data_attrs .= ' data-position-offset-y="' . esc_attr( $position_offset_y ) . '"';
		$data_attrs .= ' data-size-width="' . esc_attr( $size_width ) . '"';
    $data_attrs .= ' data-size-height="' . esc_attr( $size_height ) . '"';
    $data_attrs .= ' data-size-mobile-width="' . esc_attr( $size_mobile_width ) . '"';
    $data_attrs .= ' data-size-mobile-height="' . esc_attr( $size_mobile_height ) . '"';
		$data_attrs .= ' data-animation-appear="' . esc_attr( $animation_appear ) . '"';
		$data_attrs .= ' data-animation-exit="' . esc_attr( $animation_exit ) . '"';
		$data_attrs .= ' data-action-type="' . esc_attr( $action ) . '"';

		if ( 'url' === $action ) {
			$data_attrs .= ' data-action-url="' . esc_url( $action_url ) . '"';
			$data_attrs .= ' data-action-new-tab="' . esc_attr( $action_url_new_tab ) . '"';
		} elseif ( 'scroll' === $action ) {
			$data_attrs .= ' data-action-scroll-to="' . esc_attr( $action_scroll_to ) . '"';
			$data_attrs .= ' data-action-scroll-offset="' . esc_attr( $action_scroll_offset ) . '"';
		}

		echo '<div class="' . implode( ' ', $classes ) . '"' . $data_attrs . $style_attr . '>';

		if ( 'url' === $action && $action_url ) {
			$target = $action_url_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';
			echo '<a href="' . esc_url( $action_url ) . '"' . $target . '>';
		} elseif ( in_array( $action, array( 'scroll', 'scrolltop' ) ) ) {
			echo '<a href="#" class="sticklet__action">';
		}

		echo '<img src="' . esc_url( $image_url ) . '" alt="" class="sticklet__img" />';

		if ( in_array( $action, array( 'url', 'scroll', 'scrolltop' ) ) ) {
			echo '</a>';
		}

		echo '</div>' . "\n";
	}
}