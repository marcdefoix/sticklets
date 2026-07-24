<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$sticklets = get_posts( array(
	'post_type'      => 'sticklet',
	'posts_per_page' => -1,
	'post_status'    => 'any',
	'fields'         => 'ids',
) );

foreach ( $sticklets as $sticklet_id ) {
	wp_delete_post( $sticklet_id, true );
}