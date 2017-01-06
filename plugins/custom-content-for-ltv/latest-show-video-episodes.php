<?php

add_shortcode("latest-episodes", "LTV_Latest_Show_Videos_Small_Shortcode");
function LTV_Latest_Show_Videos_Small_Shortcode( $atts ) {
	extract( shortcode_atts( array(
		'category' => '',
		'number' => '6',
		'show_title' => false,
	), $atts ) );	
  return LTV_Latest_Show_Videos_Small( $category, $number, $show_title );
}
function LTV_Latest_Show_Videos_Small( $category, $number, $show_title ) {	

	$r = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number, 'post_type' => array('video'), 'video_category' => $category, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'offset' => 1 ) ) );
	
	echo "<ul class=\"tab\"><li><h1 class=\"tab-active\">Latest Episodes</h1></li></ul><br />";			
	if ($r->have_posts()) :  
		echo "<div style=\"float: inherit;\">";
		
		while ( $r->have_posts() ) { 
			$r->the_post();
			if ( $show_title ) {
				echo "<div class=\"video-small-wrapper\">";
			} else {
				echo "<div class=\"video-small-wrapper no-title\">";
			}
			echo get_the_post_thumbnail(get_the_id(), 'img-s');
			echo "<div class=\"video-small-overlay\">";
			echo "<a href=\"";
			echo the_permalink();
			echo "\" title=\"";
			echo esc_attr( get_the_title() ? get_the_title() : get_the_ID() ); 
			echo "\"><img src=\"";
			echo home_url();
			echo "/EN/wp-content/themes/levanttv/images/buttons/play/small.png\" /></a>";
			echo "</div>";
			if ( $show_title ) {
				echo "<h2 class=\"videos-small\"><a href=\"";
				echo the_permalink();
				echo "\" title=\"";
				echo esc_attr( get_the_title() ? get_the_title() : get_the_ID() );
				echo "\">";
				echo get_the_title();
				echo "</a></h2>";
			}
			echo "</div>"; 
		}
		echo "</div>";
		wp_reset_postdata();
	else:
		echo "<p>No items found.</p>";
	endif;

	return;
}

?>