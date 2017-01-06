<?php   

/* Remove Links from Toolbar */
function change_toolbar($wp_toolbar) {  
	$wp_toolbar->remove_node('wp-logo');
	$wp_toolbar->remove_node('new-link');
	$wp_toolbar->remove_node('new-user');
} 
add_action('admin_bar_menu', 'change_toolbar', 999);

/* Remove Links Page from Admin Menu */
function remove_links_from_admin() {
	global $menu;
	if ($menu){	
		$current_user = wp_get_current_user();
		if ($current_user->ID != '23') {
			remove_menu_page('link-manager.php');
			remove_menu_page('tools.php');
			remove_menu_page('jetpack');
			remove_menu_page('bws_plugins');
			remove_menu_page('wp-postratings/postratings-manager.php');	
		}
			remove_menu_page('edit.php?post_type=feedback');							
	}
}
add_action( 'admin_init', 'remove_links_from_admin' );

function remove_admin_bar_links() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
    $wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
    $wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
    $wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
    $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
    $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
    $wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

function replace_howdy( $wp_admin_bar ) {
	$my_account=$wp_admin_bar->get_node('my-account');
	$newtitle = str_replace( 'Howdy,', 'Logged in as', $my_account->title );           
	$wp_admin_bar->add_node( array(
		'id' => 'my-account',
		'title' => $newtitle,
	) );
}
add_filter( 'admin_bar_menu', 'replace_howdy',25 );

function remove_footer_admin () {
  echo '<a href="http://www.wordpress.org" target="_blank">WordPress</a> modified by <a href="http://www.upwardmedia.co.uk" target="_blank">Upward Media</a>';
}
add_filter('admin_footer_text', 'remove_footer_admin');

function custom_login_logo() {
	echo '
		<style type="text/css">
		.login h1 a { 
			background-image:url('.get_bloginfo('template_directory').'/images/logo.png);
			background-size:auto;
			height: 150px;
		}
		</style>
	';
}
add_action('login_head', 'custom_login_logo');