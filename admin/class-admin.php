<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sticklets_Admin {

	public function init() {
		add_action( 'init', array( 'Sticklets_Post_Type', 'register' ) );
		add_action( 'add_meta_boxes', array( 'Sticklets_Meta_Boxes', 'add_meta_boxes' ) );
		add_action( 'do_meta_boxes', array( $this, 'reposition_featured_image_metabox' ) );
		add_action( 'save_post', array( 'Sticklets_Meta_Boxes', 'save_meta' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_filter( 'manage_sticklet_posts_columns', array( $this, 'add_sticklet_columns' ) );
		add_action( 'manage_sticklet_posts_custom_column', array( $this, 'render_sticklet_columns' ), 10, 2 );
	}

	public function reposition_featured_image_metabox() {
		remove_meta_box( 'postimagediv', 'sticklet', 'side' );
		add_meta_box(
			'postimagediv',
			__( 'Sticklet Image', 'sticklets' ),
			'post_thumbnail_meta_box',
			'sticklet',
			'normal',
			'high'
		);
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
				$new_columns['position']   = __( 'Position', 'sticklets' );
				$new_columns['size']       = __( 'Size', 'sticklets' );
				$new_columns['animation']  = __( 'Animation', 'sticklets' );
				$new_columns['action']     = __( 'Action', 'sticklets' );
				$new_columns['frequency']  = __( 'Frequency', 'sticklets' );
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
				$scope      = get_post_meta( $post_id, '_sticklet_visibility', true );
				$home       = get_post_meta( $post_id, '_sticklet_visibility_home', true );
				$blog       = get_post_meta( $post_id, '_sticklet_visibility_blog', true );
				$search     = get_post_meta( $post_id, '_sticklet_visibility_search', true );
				$not_found  = get_post_meta( $post_id, '_sticklet_visibility_404', true );
				$ids_active = get_post_meta( $post_id, '_sticklet_visibility_ids_active', true );
				$ids        = get_post_meta( $post_id, '_sticklet_visibility_ids', true );

				if ( 'all' === $scope ) {
					_e( 'All', 'sticklets' );
				} elseif ( 'specific' === $scope ) {
					$parts = array();
					if ( $home ) {
						$parts[] = __( 'Home', 'sticklets' );
					}
					if ( $blog ) {
						$parts[] = __( 'Posts', 'sticklets' );
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
				$mode          = get_post_meta( $post_id, '_sticklet_trigger', true );
				$scroll_px     = intval( get_post_meta( $post_id, '_sticklet_trigger_scroll_px', true ) );
				$element       = get_post_meta( $post_id, '_sticklet_trigger_scroll_element', true );
				$click_element = get_post_meta( $post_id, '_sticklet_trigger_click_element', true );
				$bottom        = intval( get_post_meta( $post_id, '_sticklet_trigger_scroll_bottom_offset', true ) );

				switch ( $mode ) {
					case 'load':
						echo __( 'Load', 'sticklets' );
						break;
					case 'scroll_px':
						echo sprintf( __( 'Scroll %dpx', 'sticklets' ), $scroll_px );
						break;
					case 'scroll_element':
						echo __( 'Visible', 'sticklets' );
						if ( $element ) {
							echo ' — <code>' . esc_html( $element ) . '</code>';
						}
						break;
					case 'click_element':
						echo __( 'Click', 'sticklets' );
						if ( $click_element ) {
							echo ' — <code>' . esc_html( $click_element ) . '</code>';
						}
						break;
					case 'scroll_bottom':
						echo __( 'Bottom', 'sticklets' );
						if ( $bottom > 0 ) {
							echo ' — ' . esc_html( $bottom ) . 'px';
						}
						break;
					default:
						echo '—';
				}
				break;

			case 'timing':
				$delay    = intval( get_post_meta( $post_id, '_sticklet_timing_delay', true ) );
				$duration = intval( get_post_meta( $post_id, '_sticklet_timing_duration', true ) );
				$parts    = array();

				if ( $delay > 0 ) {
					$parts[] = sprintf( __( '%dms delay', 'sticklets' ), $delay );
				}
				if ( $duration > 0 ) {
					$parts[] = sprintf( __( '%dms duration', 'sticklets' ), $duration );
				}

				if ( empty( $parts ) ) {
					echo '—';
				} else {
					echo esc_html( implode( ' | ', $parts ) );
				}
				break;

			case 'position':
				$position_y = get_post_meta( $post_id, '_sticklet_position_y', true ) ?: 'y-bottom';
				$position_x = get_post_meta( $post_id, '_sticklet_position_x', true ) ?: 'x-right';
				$offset_x   = intval( get_post_meta( $post_id, '_sticklet_position_offset_x', true ) );
				$offset_y   = intval( get_post_meta( $post_id, '_sticklet_position_offset_y', true ) );
				$parts      = array();

				$parts[] = ucfirst( str_replace( 'y-', '', $position_y ) );
				$parts[] = ucfirst( str_replace( 'x-', '', $position_x ) );
				if ( $offset_x !== 0 || $offset_y !== 0 ) {
					$parts[] = sprintf( 'X:%d Y:%d', $offset_x, $offset_y );
				}

				echo esc_html( implode( ' / ', $parts ) );
				break;

			case 'size':
				$width         = intval( get_post_meta( $post_id, '_sticklet_size_width', true ) );
				$height        = intval( get_post_meta( $post_id, '_sticklet_size_height', true ) );
				$mobile_width  = intval( get_post_meta( $post_id, '_sticklet_size_mobile_width', true ) );
				$mobile_height = intval( get_post_meta( $post_id, '_sticklet_size_mobile_height', true ) );
				$parts         = array();

				if ( $width > 0 || $height > 0 ) {
					$desktop = array();
					if ( $width > 0 ) {
						$desktop[] = $width . 'px';
					}
					if ( $height > 0 ) {
						$desktop[] = $height . 'px';
					}
					$parts[] = __( 'Desktop', 'sticklets' ) . ': ' . implode( '×', $desktop );
				}

				if ( $mobile_width > 0 || $mobile_height > 0 ) {
					$mobile = array();
					if ( $mobile_width > 0 ) {
						$mobile[] = $mobile_width . 'px';
					}
					if ( $mobile_height > 0 ) {
						$mobile[] = $mobile_height . 'px';
					}
					$parts[] = __( 'Mobile', 'sticklets' ) . ': ' . implode( '×', $mobile );
				}

				if ( empty( $parts ) ) {
					_e( 'Auto', 'sticklets' );
				} else {
					echo esc_html( implode( ' | ', $parts ) );
				}
				break;

			case 'animation':
				$appear = get_post_meta( $post_id, '_sticklet_animation_appear', true ) ?: 'none';
				$exit   = get_post_meta( $post_id, '_sticklet_animation_exit', true ) ?: 'none';
				$parts  = array();

				if ( 'none' !== $appear ) {
					$parts[] = ucfirst( str_replace( '-', ' ', $appear ) );
				}
				if ( 'none' !== $exit ) {
					$parts[] = ucfirst( str_replace( '-', ' ', $exit ) );
				}

				if ( empty( $parts ) ) {
					echo '—';
				} else {
					echo esc_html( implode( ' | ', $parts ) );
				}
				break;

			case 'action':
				$action           = get_post_meta( $post_id, '_sticklet_action', true ) ?: 'none';
				$action_url       = get_post_meta( $post_id, '_sticklet_action_url', true );
				$action_scroll_to = get_post_meta( $post_id, '_sticklet_action_scroll_to', true );

				switch ( $action ) {
					case 'none':
						echo '—';
						break;

					case 'url':
						if ( $action_url ) {
							echo esc_html( wp_parse_url( $action_url, PHP_URL_HOST ) ?: $action_url );
						} else {
							echo '—';
						}
						break;

					case 'scroll':
						echo __( 'Scroll to', 'sticklets' );
						if ( $action_scroll_to ) {
							echo ' <code>' . esc_html( $action_scroll_to ) . '</code>';
						}
						break;

					case 'scrolltop':
						_e( 'Top', 'sticklets' );
						break;

					default:
						echo '—';
				}
				break;

			case 'frequency':
				$frequency = get_post_meta( $post_id, '_sticklet_frequency', true ) ?: 'always';
				$times     = intval( get_post_meta( $post_id, '_sticklet_frequency_times', true ) );

				switch ( $frequency ) {
					case 'always':
						_e( 'Always', 'sticklets' );
						break;

					case 'once':
						_e( 'Once', 'sticklets' );
						break;

					case 'times':
						if ( $times > 0 ) {
							echo sprintf( __( '%d times', 'sticklets' ), $times );
						} else {
							_e( 'Limited', 'sticklets' );
						}
						break;

					default:
						echo '—';
				}
				break;
		}
	}
}