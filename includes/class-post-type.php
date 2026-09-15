<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sticklets_Post_Type {
	public static function register() {
		$labels = array(
			'name'               => _x( 'Sticklets', 'post type general name', 'sticklets' ),
			'singular_name'      => _x( 'Sticklet', 'post type singular name', 'sticklets' ),
			'menu_name'          => _x( 'Sticklets', 'admin menu', 'sticklets' ),
			'name_admin_bar'     => _x( 'Sticklet', 'add new on admin bar', 'sticklets' ),
			'add_new'            => _x( 'Add New', 'sticklet', 'sticklets' ),
			'add_new_item'       => __( 'Add New Sticklet', 'sticklets' ),
			'new_item'           => __( 'New Sticklet', 'sticklets' ),
			'edit_item'          => __( 'Edit Sticklet', 'sticklets' ),
			'view_item'          => __( 'Edit Sticklet', 'sticklets' ),
			'all_items'          => __( 'All Sticklets', 'sticklets' ),
			'search_items'       => __( 'Search Sticklets', 'sticklets' ),
			'parent_item_colon'  => __( 'Parent Sticklets:', 'sticklets' ),
			'not_found'          => __( 'No sticklets found.', 'sticklets' ),
			'not_found_in_trash' => __( 'No sticklets found in Trash.', 'sticklets' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 30,
			'menu_icon'           => 'dashicons-format-image',
			'supports'            => array( 'title', 'thumbnail' ),
			'show_in_rest'        => false,
		);

		register_post_type( 'sticklet', $args );
	}
}