<?php

/* Create Custom Post Type */
function wpcu_videos_register_post_types() {
	$labels = array(
		'name'               => __( 'Videos' ),
		'singular_name'      => __( 'Video' ),
		'add_new'            => __( 'Add New Video' ),
		'add_new_item'       => __( 'Add New Video' ),
		'edit_item'          => __( 'Edit Video' ),
		'new_item'           => __( 'New Video' ),
		'all_items'          => __( 'All Videos' ),
		'view_item'          => __( 'View Video' ),
		'search_items'       => __( 'Search Videos' ),
		'not_found'          => __( 'No Videos found' ),
		'not_found_in_trash' => __( 'No Videos found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Videos'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'Holds our videos and video specific data',
        'public' => true,
        'query_var' => 'videos',
        'rewrite' => array(
            'slug' => 'videos',
            'with_front' => false,
        ),
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'comments' ),
		'has_archive'   => true,
		'taxonomies' => array('post_tag'),
	);
	register_post_type( 'video', $args );
	flush_rewrite_rules( false );	
}
add_action( 'init', 'wpcu_videos_register_post_types' );


/* Create Custom Taxonomy */
function wpcu_videos_register_taxonomies() {
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
	register_taxonomy( 'video_category', 'video', $args );
}
add_action( 'init', 'wpcu_videos_register_taxonomies', 0 );


/* Defining the Custom Meta Box */
add_action( 'add_meta_boxes', 'wpcu_videos_youtube_url_box' );
function wpcu_videos_youtube_url_box() {
    add_meta_box( 
        'wpcu_videos_youtube_url_box',
        __( 'YouTube URL', 'plugin_textdomain' ),
        'wpcu_videos_youtube_url_box_content',
        'video',
        'normal',
        'high'
    );
}


/* Defining the Content of the Meta Box */
function wpcu_videos_youtube_url_box_content( $post ) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'wpcu_videos_youtube_url_box_content_nonce' );
	
	// Get the video url if its already been entered
	$video_url = get_post_meta($post->ID, 'video_url', true);
	
	echo '<label for="video_url">http://www.youtube.com/watch?v= </label>
		  <input type="text" name="video_url" id="video_url" value="' . $video_url . '" />';
}


/* Handling Submitted Data */
add_action( 'save_post', 'wpcu_videos_youtube_url_box_save' );
function wpcu_videos_youtube_url_box_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	return;

	if ( !wp_verify_nonce( $_POST['wpcu_videos_youtube_url_box_content_nonce'], plugin_basename( __FILE__ ) ) )
	return;

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}
	$video_url = $_POST['video_url'];
	update_post_meta( $post_id, 'video_url', $video_url );
}

?>