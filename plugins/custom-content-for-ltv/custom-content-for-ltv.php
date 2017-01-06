<?php

/*
Plugin Name: Custom Content for Levant TV
Description: Collection of custom plugins for Levant TV
Version: 1.4.2
Author: Pia Storck, Upward Media
Author URI: http://www.upwardmedia.co.uk
*/

require_once('videos-post-type.php');
require_once('shows-post-type.php');
require_once('blog-post-type.php');
require_once('frontpage-videos.php');
require_once('latest-news-articles.php');
require_once('latest-show-video.php');
require_once('latest-show-video-episodes.php');
require_once('filter.php');
require_once('filter-by-date.php');
require_once('backend-modification-by-upward-media.php');

add_shortcode("tab", "LTV_Display_Title_As_Tab");
function LTV_Display_Title_As_Tab( $atts ) {
	extract( shortcode_atts( array(
		'title' => '',
	), $atts ) );
  return "<ul class=\"tab\"><li><h1 class=\"tab-active\">" . $title . "</h1></li></ul>";
}

?>