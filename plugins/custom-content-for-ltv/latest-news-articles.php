<?php

add_shortcode("latest-news-articles", "LTV_Latest_News_Articles_Shortcode");
function LTV_Latest_News_Articles_Shortcode( $atts ) {
	extract( shortcode_atts( array(
		'category' => '',
		'number' => '2',
	), $atts ) );	
  return LTV_Latest_News_Articles( $category, $number );
}

function LTV_Latest_News_Articles( $category, $number ) {	
	
	echo "<ul class=tab><li><h1 class=\"tab-active\"><a href=\"" . home_url() . "/category/news/" . $category . "\">";
	echo get_category_by_slug($category)->name;
	echo "</a></h1></li></ul><br />";
	
	$args = array(
		'category_name' => $category,
		'offset' => 0,
		'showposts' => $number
	);
	
	if(isset($args)) query_posts( $args );
	
		echo "<table width=\"633px\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"archive\"><tr>";								
		if (have_posts()) {
			while (have_posts()) { 
				the_post();
				echo "<tr><td>";                   
				if ( has_post_thumbnail() ) {
					echo "<a href=\"";
					echo the_permalink(); 
					echo "\">";
					echo the_post_thumbnail('img-s');
					echo "</a>";
				}
				echo "</td><td>";           
				echo "<h2><a href=\"";
				echo the_permalink(); 
				echo "\">";
				echo the_title();
				echo "</a></h2>";
				echo "<p class=\"meta\">Posted by ";
				echo the_author_posts_link();
				echo " on ";
				echo the_time("l, d.m.Y");
				echo "</p>";				
				the_excerpt();
				echo "</td></tr>";                        
		   }
		} else {
			echo "<p>No items found.</p>";
		}
		echo "</tr></table>";	

	return;
}

?>