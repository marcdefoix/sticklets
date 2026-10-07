<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit;
}

do {
  $sticklets = get_posts( array(
    'post_type'      => 'sticklet',
    'posts_per_page' => 100,
    'post_status'    => 'any',
    'fields'         => 'ids',
  ) );

  foreach ( $sticklets as $sticklet_id ) {
    wp_delete_post( $sticklet_id, true );
  }
} while ( ! empty( $sticklets ) );

/*
  sticklet_trigger_scroll_px ha de ser >= 0 valor per defecte 0 (px)
  sticklet_trigger_scroll_element_offset pot ser negatiu o positiu valor per defecte 0 (px)
  sticklet_trigger_scroll_bottom_offset ha de ser >= 0 valor per defecte 0 (px)
  sticklet_timing_duration ha de ser >= 0 valor per defecte 2500 (ms)
  sticklet_timing_delay ha de ser >= 0 valor per defecte 0 (ms)
  sticklet_position_offset_x pot ser negatiu o positiu valor per defecte 0 (px)
  sticklet_position_offset_y pot ser negatiu o positiu valor per defecte 0 (px)
  sticklet_size_width ha de ser >= 1 valor per defecte 100 (px)
  sticklet_size_height ha de ser >= 1 valor per defecte 100 (px)
  sticklet_size_mobile_width ha de ser >= 1 valor per defecte 32 (px)
  sticklet_size_mobile_height ha de ser >= 1 valor per defecte 32 (px)
  sticklet_action_scroll_to_offset pot ser negatiu o positiu valor per defecte 0 (px)
  sticklet_action_scroll_top_offset ha de ser >= 0 valor per defecte 0 (px)
  sticklet_frequency_times ha de ser >= 1 valor per defecte 1 (vegades)
*/