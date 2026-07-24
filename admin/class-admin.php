<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sticklets_Admin {

	public function init() {
		add_action( 'init', array( 'Sticklets_Post_Type', 'register' ) );
		add_action( 'add_meta_boxes', array( 'Sticklets_Meta_Boxes', 'add_meta_boxes' ) );
		add_action( 'save_post', array( 'Sticklets_Meta_Boxes', 'save_meta' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_filter( 'manage_sticklet_posts_columns', array( $this, 'add_sticklet_columns' ) );
		add_action( 'manage_sticklet_posts_custom_column', array( $this, 'render_sticklet_columns' ), 10, 2 );
	}

	public function enqueue_admin_assets( $hook ) {
		global $post_type;

		if ( 'sticklet' !== $post_type ) {
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
			array( 'jquery' ),
			STICKLETS_VERSION,
			true
		);
	}

	public function add_sticklet_columns( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $value ) {
			$new_columns[ $key ] = $value;

			if ( 'title' === $key ) {
				$new_columns['thumbnail']  = __( 'Image', 'sticklets' );
				$new_columns['visibility'] = __( 'Visibility', 'sticklets' );
				$new_columns['trigger']    = __( 'Trigger', 'sticklets' );
				$new_columns['timing']     = __( 'Timing', 'sticklets' );
				$new_columns['size']       = __( 'Size', 'sticklets' );
				$new_columns['position']   = __( 'Position', 'sticklets' );
				$new_columns['animation']  = __( 'Animation', 'sticklets' );
				$new_columns['click_url']  = __( 'Click URL', 'sticklets' );
			}
		}

		return $new_columns;
	}

	public function render_sticklet_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'thumbnail':
				$image_id = get_post_thumbnail_id( $post_id );
				if ( $image_id ) {
					echo wp_get_attachment_image( $image_id, array( 48, 48 ), false, array(
						'style' => 'max-width:48px; height:auto; display:block; border-radius:2px;',
					) );
				} else {
					echo '<span style="color:#a7aaad;">—</span>';
				}
				break;

			case 'visibility':
				$scope      = get_post_meta( $post_id, '_sticklet_visibility_scope', true );
				$home       = get_post_meta( $post_id, '_sticklet_visibility_home', true );
				$blog       = get_post_meta( $post_id, '_sticklet_visibility_blog', true );
				$search     = get_post_meta( $post_id, '_sticklet_visibility_search', true );
				$not_found  = get_post_meta( $post_id, '_sticklet_visibility_404', true );
				$ids_active = get_post_meta( $post_id, '_sticklet_visibility_ids_active', true );
				$ids        = get_post_meta( $post_id, '_sticklet_visibility_ids', true );

				if ( 'all' === $scope ) {
					_e( 'Entire site', 'sticklets' );
				} elseif ( 'specific' === $scope ) {
					$parts = array();
					if ( $home ) {
						$parts[] = __( 'Homepage', 'sticklets' );
					}
					if ( $blog ) {
						$parts[] = __( 'Posts page', 'sticklets' );
					}
					if ( $search ) {
						$parts[] = __( 'Search', 'sticklets' );
					}
					if ( $not_found ) {
						$parts[] = __( '404', 'sticklets' );
					}
					if ( $ids_active && ! empty( $ids ) ) {
						$parts[] = esc_html( $ids );
					}
					if ( empty( $parts ) ) {
						echo '—';
					} else {
						echo implode( ' + ', $parts );
					}
				} else {
					echo '—';
				}
				break;

			case 'trigger':
				$mode     = get_post_meta( $post_id, '_sticklet_trigger_mode', true );
				$specific = get_post_meta( $post_id, '_sticklet_trigger_specific', true );
				$scroll_px = get_post_meta( $post_id, '_sticklet_trigger_scroll_px', true );
				$element  = get_post_meta( $post_id, '_sticklet_trigger_scroll_element', true );
				$bottom   = get_post_meta( $post_id, '_sticklet_trigger_scroll_bottom_offset', true );

				if ( 'load' === $mode ) {
					_e( 'On page load', 'sticklets' );
				} elseif ( 'specific' === $mode ) {
					if ( 'scroll_px' === $specific ) {
						_e( 'After scrolling', 'sticklets' );
						echo ' — ' . esc_html( $scroll_px ) . ' px';
					} elseif ( 'scroll_element' === $specific ) {
						_e( 'When element visible', 'sticklets' );
						if ( $element ) {
							echo ' — <code>' . esc_html( $element ) . '</code>';
						}
					} elseif ( 'scroll_bottom' === $specific ) {
						_e( 'Page bottom', 'sticklets' );
						if ( $bottom > 0 ) {
							echo ' — ' . esc_html( $bottom ) . ' px offset';
						}
					}
				} else {
					echo '—';
				}
				break;

			case 'timing':
				$delay    = intval( get_post_meta( $post_id, '_sticklet_timing_delay', true ) );
				$duration = intval( get_post_meta( $post_id, '_sticklet_timing_duration', true ) );
				$parts    = array();

				if ( $delay > 0 ) {
					$parts[] = sprintf( __( 'Delay: %d ms', 'sticklets' ), $delay );
				}
				if ( $duration > 0 ) {
					$parts[] = sprintf( __( 'Duration: %d ms', 'sticklets' ), $duration );
				}

				if ( empty( $parts ) ) {
					echo '—';
				} else {
					echo esc_html( implode( ' | ', $parts ) );
				}
				break;

			case 'size':
				$size_mode = get_post_meta( $post_id, '_sticklet_size_mode', true ) ?: 'original';
				$width     = intval( get_post_meta( $post_id, '_sticklet_size_width', true ) );
				$height    = intval( get_post_meta( $post_id, '_sticklet_size_height', true ) );

				if ( 'cropped' === $size_mode ) {
					$parts = array( __( 'Cropped', 'sticklets' ) );
					if ( $width > 0 ) {
						$parts[] = $width . ' px';
					}
					if ( $height > 0 ) {
						$parts[] = $height . ' px';
					}
					echo esc_html( implode( ' × ', $parts ) );
				} elseif ( 'original' === $size_mode ) {
					_e( 'Original', 'sticklets' );
				} else {
					echo '—';
				}
				break;

			case 'position':
				$position_y = get_post_meta( $post_id, '_sticklet_position_y', true ) ?: 'y-bottom';
				$position_x = get_post_meta( $post_id, '_sticklet_position_x', true ) ?: 'x-right';
				$offset_x   = intval( get_post_meta( $post_id, '_sticklet_position_offset_x', true ) );
				$offset_y   = intval( get_post_meta( $post_id, '_sticklet_position_offset_y', true ) );
				$parts      = array();

				$parts[] = str_replace( 'y-', '', $position_y );
				$parts[] = str_replace( 'x-', '', $position_x );
				if ( $offset_x ) {
					$parts[] = sprintf( __( 'X offset: %d px', 'sticklets' ), $offset_x );
				}
				if ( $offset_y ) {
					$parts[] = sprintf( __( 'Y offset: %d px', 'sticklets' ), $offset_y );
				}

				echo esc_html( implode( ' • ', $parts ) );
				break;

			case 'animation':
				$appear = get_post_meta( $post_id, '_sticklet_animation_appear', true ) ?: 'none';
				$exit   = get_post_meta( $post_id, '_sticklet_animation_exit', true ) ?: 'none';
				$parts  = array();

				if ( 'none' !== $appear ) {
					$parts[] = sprintf( __( 'Appear: %s', 'sticklets' ), ucfirst( $appear ) );
				}
				if ( 'none' !== $exit ) {
					$parts[] = sprintf( __( 'Exit: %s', 'sticklets' ), ucfirst( $exit ) );
				}

				if ( empty( $parts ) ) {
					echo '—';
				} else {
					echo esc_html( implode( ' | ', $parts ) );
				}
				break;

			case 'click_url':
				$url = get_post_meta( $post_id, '_sticklet_click_url', true );
				if ( $url ) {
					echo esc_html( $url );
				} else {
					echo '—';
				}
				break;
		}
	}
}