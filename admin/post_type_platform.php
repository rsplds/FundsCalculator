<?php

/**
 *  Register Custom Post Type Platform
 *
 */
function register_platform() {

	$platform_labels = array(
		'name'               => __( 'Platforms', 'fundscape_calculations' ),
		'singular_name'      => __( 'Platform', 'fundscape_calculations' ),
		'add_new'            => _x( 'Add New Platform', 'fundscape_calculations', 'fundscape_calculations' ),
		'add_new_item'       => __( 'Add New Platform', 'fundscape_calculations' ),
		'edit_item'          => __( 'Edit Platform', 'fundscape_calculations' ),
		'new_item'           => __( 'New Platform', 'fundscape_calculations' ),
		'view_item'          => __( 'View Platform', 'fundscape_calculations' ),
		'search_items'       => __( 'Search Platforms', 'fundscape_calculations' ),
		'not_found'          => __( 'No Platform found', 'fundscape_calculations' ),
		'not_found_in_trash' => __( 'No Platforms found in Trash', 'fundscape_calculations' ),
		'parent_item_colon'  => __( 'Parent Platform:', 'fundscape_calculations' ),
		'menu_name'          => __( 'Platforms', 'fundscape_calculations' ),
	);
	$platform_args = array(
		'labels'              => $platform_labels,
		'hierarchical'        => false,
		'description'         => 'description',
		'taxonomies'          => array(),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => null,
		'menu_icon'           => null,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'supports'            => array( 'title', 'author', 'custom-fields', 'revisions' ),
	);
	register_post_type( 'platform', $platform_args );
}

add_action( 'init', 'register_platform' );

?>