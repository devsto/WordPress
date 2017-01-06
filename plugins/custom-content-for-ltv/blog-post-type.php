<?php

/* Create Custom Post Type */
function wpcu_blog_posts_register_post_types() {
	$labels = array(
		'name'               => __( 'Blog Posts' ),
		'singular_name'      => __( 'Blog Post' ),
		'add_new'            => __( 'Add New Blog Post' ),
		'add_new_item'       => __( 'Add New Blog Post' ),
		'edit_item'          => __( 'Edit Blog Post' ),
		'new_item'           => __( 'New Blog Post' ),
		'all_items'          => __( 'All Blog Posts' ),
		'view_item'          => __( 'View Blog Post' ),
		'search_items'       => __( 'Search Blog Posts' ),
		'not_found'          => __( 'No Blog Posts found' ),
		'not_found_in_trash' => __( 'No Blog Posts found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Blog Posts'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'Holds our blog posts and blog post specific data',
        'public' => true,
        'query_var' => 'blog_posts',
        'rewrite' => array(
            'slug' => 'blog',
            'with_front' => false,
        ),
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'comments' ),
		'has_archive'   => true,
		'taxonomies' => array('post_tag'),
	);
	register_post_type( 'blog_post', $args );	
}
add_action( 'init', 'wpcu_blog_posts_register_post_types' );


/* Create Custom Taxonomy */
function wpcu_blog_posts_register_taxonomies() {
	$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Categories' ),
		'all_items'         => __( 'All Categories' ),
		'parent_item'       => __( 'Parent Category' ),
		'parent_item_colon' => __( 'Parent Category:' ),
		'edit_item'         => __( 'Edit Category' ), 
		'update_item'       => __( 'Update Category' ),
		'add_new_item'      => __( 'Add New Category' ),
		'new_item_name'     => __( 'New Category' ),
		'menu_name'         => __( 'Categories' ),
	);
	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
	);
	register_taxonomy( 'blog_post_category', 'blog_post', $args );
}
add_action( 'init', 'wpcu_blog_posts_register_taxonomies', 0 );


?>