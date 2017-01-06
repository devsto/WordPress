<?php

add_shortcode("frontpage-videos", "LTV_Frontpage_Videos_Shortcode");
function LTV_Frontpage_Videos_Shortcode( $atts ) {
	extract( shortcode_atts( array(
		'number' => '',
	), $atts ) );	
  	return LTV_Frontpage_Videos( $number );
}

function LTV_Frontpage_Videos( $number ) {	
		
	if (isset($_GET['category']) and $_GET['category'] != '') {
		$category = $_GET['category'];
	} else {
		$category = 'political-shows';
	}

	if (isset($_GET['order_by']) and $_GET['order_by'] != '') {
		$order_by = $_GET['order_by'];
	} else {
		$order_by = 'post_date';
	}
	
	
	echo "<ul class=\"tab\"><li>";
	if ($category == 'political-shows') {
		echo "<h1 class=\"tab-active\">Political Shows</h1>";
	} else {
		echo "<a href=\"" . home_url() . "/index.php?category=political-shows\"><h1>Political Shows</h1></a>";
	}
	echo "</li><li>";
	if ($category == 'entertainment-shows') { 
		echo "<h1 class=\"tab-active\">Entertainment Shows</h1>";
	} else {
		echo "<a href=\"" . home_url() . "\"/index.php?category=entertainment-shows\"><h1>Entertainment Shows</h1></a>";
	} 
	echo "</li></ul><br />";
	
	?>
		
	<ul class="filter">
		<li <?php if ($order_by == 'post_date') echo "class=\"filter-active\""; ?>><a href="<?php echo home_url(); ?>/index.php/?category=<?php echo $category; ?>&order_by=post_date">Latest</a></li>
		<li <?php if ($order_by == 'recommended') echo "class=\"filter-active\""; ?>><a href="<?php echo home_url(); ?>/index.php/?category=<?php echo $category; ?>&order_by=recommended">Recommended</a></li>
		<li <?php if ($order_by == 'most_viewed') echo "class=\"filter-active\""; ?>><a href="<?php echo home_url(); ?>/index.php/?category=<?php echo $category; ?>&order_by=most_viewed">Most Viewed</a></li>
		<li <?php if ($order_by == 'highest_rated') echo "class=\"filter-active\""; ?>><a href="<?php echo home_url(); ?>/index.php/?category=<?php echo $category; ?>&order_by=highest_rated">Highest Rated</a></li>
	</ul>
	<br />		
        
        
	<?php
		
	if ( $order_by == 'recommended' ) {

		$args = array(
			'posts_per_page' => $number,
			'post_type' => 'video',
			'tax_query' => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'video_category',
					'field' => 'slug',
					'terms' => array( $category )
				),
				array(
					'taxonomy' => 'video_category',
					'field' => 'slug',
					'terms' => array( 'recommended' )
				)
			),
			'post_status' => 'publish',
			'order' => 'DESC',
			'no_found_rows' => true,
			'ignore_sticky_posts' => true
		);
		$r = new WP_Query( $args );

	} elseif ( $order_by == 'highest_rated' ) {

		// Highest rated videos
		$r = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number, 'post_type' => array('video'), 'video_category' => $category, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'r_sortby'=>'highest_rated' ) ) );

	} elseif ( $order_by == 'most_viewed' ) {

		// Most viewed videos
		global $wpdb;
		$category_id = $wpdb->get_var("SELECT ".$wpdb->prefix."term_taxonomy.term_taxonomy_id FROM ".$wpdb->prefix."term_taxonomy LEFT JOIN ".$wpdb->prefix."terms ON ".$wpdb->prefix."term_taxonomy.term_id = ".$wpdb->prefix."terms.term_id WHERE ".$wpdb->prefix."terms.slug = '". $category ."'");

		$querystr = "SELECT DISTINCT wp_posts.* FROM wp_posts LEFT JOIN wp_popularpostsdata ON wp_posts.ID = wp_popularpostsdata.postid LEFT JOIN wp_term_relationships ON wp_posts.ID = wp_term_relationships.object_id
		WHERE wp_posts.post_type = 'video' AND wp_posts.post_status = 'publish' AND wp_term_relationships.term_taxonomy_id = ". $category_id . " ORDER BY wp_popularpostsdata.pageviews DESC LIMIT 9 OFFSET 0";

	} else {
			
		// Latest videos
		$r = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number, 'post_type' => array('video'), 'video_category' => $category, 'orderby' => $order_by, 'order' => 'DESC', 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );

	}

	if (isset($querystr)) {
		global $wpdb;
		$pageposts = $wpdb->get_results($querystr, OBJECT);

		if ($pageposts) {
			global $post;
			foreach ($pageposts as $post) { 
				setup_postdata($post);
				LTV_Frontpage_Videos_Display();
			}
		} else {
			echo "<p>No items found.</p>";
		} 
	} elseif ($r->have_posts()) {
		while ( $r->have_posts() ) { 
			$r->the_post(); 
			LTV_Frontpage_Videos_Display();
		}
		wp_reset_postdata();
	} else {
		echo "<p>No items found.</p>";
	}	

	return;
}

function LTV_Frontpage_Videos_Display() {
	echo"<div class=\"video-small-wrapper\">";
    echo get_the_post_thumbnail(get_the_id(), 'img-s');
    echo "<div class=\"video-small-overlay\">
          	<a href=\"";
			the_permalink(); 
			echo "\" title=\"";
			echo esc_attr( get_the_title() ? get_the_title() : get_the_ID() );
			echo "\"><img src=\"" . home_url() . "/EN/wp-content/themes/levanttv/images/buttons/play/small.png\" /></a>
                </div>
                <h2 class=\"videos-small\"><a href=\"" . the_permalink() . "\" title=\"";
				echo esc_attr( get_the_title() ? get_the_title() : get_the_ID() );
				echo "\">" . get_the_title() . "</a></h2>
            </div>";        
}	

?>