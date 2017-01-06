<?php

add_shortcode("latest-show-video", "LTV_Latest_Show_Video_Shortcode");
function LTV_Latest_Show_Video_Shortcode( $atts ) {
	extract( shortcode_atts( array(
		'category' => '',
	), $atts ) );	
  return LTV_Latest_Show_Video( $category );
}
function LTV_Latest_Show_Video( $category ) {	

	$r = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => 1, 'post_type' => array('video'), 'video_category' => $category, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );
	
	echo "<ul class=\"tab\"><li><h1 class=\"tab-active\">Episode of the Week</h1></li></ul><br />";		
	if ($r->have_posts()) {
		while ( $r->have_posts() ) { 
			$r->the_post();
			echo "<div class=\"video-large-wrapper\">";
			echo get_the_post_thumbnail(get_the_id(), 'img-l'); 
			echo "<div class=\"video-large-overlay\">";
			echo "<a href=\"";
			echo the_permalink();
			echo "\" title=\"";
			echo esc_attr( get_the_title() ? get_the_title() : get_the_ID() );
			echo "\"><img src=\"";
			echo home_url();
			echo "/EN/wp-content/themes/levanttv/images/buttons/play/large.png\" /></a>";
			echo "</div>";
			echo "</div>";
		}
		wp_reset_postdata();
	} else {
		echo "<p>No items found.</p>";
	}

	return;
}

?>