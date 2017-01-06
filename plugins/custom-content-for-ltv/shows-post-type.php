<?php

/* Create Custom Post Type */
function wpcu_shows_register_post_types() {
	$labels = array(
		'name'               => __( 'Shows' ),
		'singular_name'      => __( 'Show' ),
		'add_new'            => __( 'Add New Show' ),
		'add_new_item'       => __( 'Add New Show' ),
		'edit_item'          => __( 'Edit Show' ),
		'new_item'           => __( 'New Show' ),
		'all_items'          => __( 'All Shows' ),
		'view_item'          => __( 'View Show' ),
		'search_items'       => __( 'Search Shows' ),
		'not_found'          => __( 'No Shows found' ),
		'not_found_in_trash' => __( 'No Shows found in the Trash' ), 
		'parent'             => __( 'Parent Show' ),
		'parent_item_colon'  => '',
		'menu_name'          => 'Shows'
	);
	$args = array(
		'labels'        => $labels,
		'description'   => 'Holds our Shows and Show specific data',
        'public' => true,
        'query_var' => 'shows',
        'rewrite' => array(
            'slug' => 'shows',
            'with_front' => false,
        ),
		'menu_position' => 5,
		'hierarchical'  => true,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'comments', 'page-attributes' ),
		'has_archive'   => false,
	);
	register_post_type( 'show', $args );
	flush_rewrite_rules( false );	
}
add_action( 'init', 'wpcu_shows_register_post_types' );


/* Defining the Custom Meta Box */
add_action( 'add_meta_boxes', 'wpcu_shows_this_week_box' );
function wpcu_shows_this_week_box() {
    add_meta_box( 
        'wpcu_shows_this_week_box',
        __( 'This Week\'s Show', 'plugin_textdomain' ),
        'wpcu_shows_this_week_box_content',
        'show',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wpcu_shows_about_box' );
function wpcu_shows_about_box() {
    add_meta_box( 
        'wpcu_shows_about_box',
        __( 'About the Show', 'plugin_textdomain' ),
        'wpcu_shows_about_box_content',
        'show',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wpcu_shows_fb_url_box' );
function wpcu_shows_fb_url_box() {
    add_meta_box( 
        'wpcu_shows_fb_url_box',
        __( 'Facebook', 'plugin_textdomain' ),
        'wpcu_shows_fb_url_box_content',
        'show',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'wpcu_shows_twitter_widget_box' );
function wpcu_shows_twitter_widget_box() {
    add_meta_box( 
        'wpcu_shows_twitter_widget_box',
        __( 'Twitter', 'plugin_textdomain' ),
        'wpcu_shows_twitter_widget_box_content',
        'show',
        'normal',
        'high'
    );
}


/* Defining the Content of the Meta Box */
function wpcu_shows_this_week_box_content( $post ) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'wpcu_shows_this_week_box_content_nonce' );
	
	$this_week = get_post_meta($post->ID, 'this_week', true);
	
	echo '<textarea name="this_week" id="this_week" cols="80" rows="5">' . $this_week . '</textarea>
	<p>Leave empty if subpage of a show.</p>';
}
function wpcu_shows_about_box_content( $post ) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'wpcu_shows_about_box_content_nonce' );
	
	$about = get_post_meta($post->ID, 'about', true);
	
	echo '<textarea name="about" id="about" cols="80" rows="5">' . $about . '</textarea>
	<p>Leave empty if subpage of a show.</p>';
}
function wpcu_shows_fb_url_box_content( $post ) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'wpcu_shows_fb_url_box_content_nonce' );
	
	$fb_url = get_post_meta($post->ID, 'fb_url', true);
	
	echo '<label for="fb_url">Facebook URL to comment on: </label>
	<input type="text" name="fb_url" id="fb_url" value="' . $fb_url . '" />
	<p>Leave empty if subpage of a show.</p>';
}
function wpcu_shows_twitter_widget_box_content( $post ) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'wpcu_shows_twitter_widget_box_content_nonce' );
	
	$twitter_widget = get_post_meta($post->ID, 'twitter_widget', true);
	
	echo '<textarea name="twitter_widget" id="twitter_widget" cols="80" rows="5">' . $twitter_widget . '</textarea>
	<p>To set up twitter widget, visit https://twitter.com/settings/widgets<br>Create new widget, copy code and paste it into the field above.</p>';
}

/* Handling Submitted Data */
add_action( 'save_post', 'wpcu_shows_field_box_save' );
function wpcu_shows_field_box_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	return;

	if ( !wp_verify_nonce( $_POST['wpcu_shows_this_week_box_content_nonce'], plugin_basename( __FILE__ ) ) )
	return;
	if ( !wp_verify_nonce( $_POST['wpcu_shows_about_box_content_nonce'], plugin_basename( __FILE__ ) ) )
	return;

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}
	$this_week = $_POST['this_week'];
	update_post_meta( $post_id, 'this_week', $this_week );
	
	$about = $_POST['about'];
	update_post_meta( $post_id, 'about', $about );
	
	$fb_url = $_POST['fb_url'];
	update_post_meta( $post_id, 'fb_url', $fb_url );
	
	$twitter_widget = $_POST['twitter_widget'];
	update_post_meta( $post_id, 'twitter_widget', $twitter_widget );

}

?>