<?php

/* Order content by date, recommended, highest rated or most viewed */

add_shortcode("filter-items", "LTV_Filter_Shortcode");

function LTV_Filter_Shortcode( $atts ) {
	extract( shortcode_atts( array(
		'number' => '',
	), $atts ) );	
  return LTV_Filter( $number );
}

function LTV_Filter( $number = '' ) {
	
	global $wp_query;

	$category = get_query_var($wp_query->query_vars['taxonomy']); // e.g. political-blog
	$post_type = get_post_type();
	$taxonomy = $wp_query->tax_query->queries[0]['taxonomy'];

	if (isset($number) && $number != '') {
		$showposts = $number;
	} else {
		$showposts = get_query_var('posts_per_page');
	}

	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$offset = $showposts * ( $paged-1 );

	if (isset($_GET['order_by']) and $_GET['order_by'] != '') {
		$order_by = $_GET['order_by'];
	} else {
		$order_by = 'post_date';
	}

	$post_type = get_post_type();
	if ($post_type == 'post') {
		// get path
		$parentcatlist = strtolower(get_category_parents($cat, false, '/'));
		$replace = array(" " => "-", "(" => "", ")" => "");
		$path = strtolower(strtr($parentcatlist,$replace));
		$url = home_url()."/category/".$path;
	} else {
		$tax = $wp_query->tax_query->queries[0]['taxonomy'];
		$url = home_url()."/".$tax."/".$category."/";
	}

?>
        
<ul class="filter">
	<li <?php if ($order_by == 'post_date') echo "class=\"filter-active\""; ?>><a href="<?php echo $path; ?>?order_by=post_date">Latest</a></li>
	<li <?php if ($order_by == 'recommended') echo "class=\"filter-active\""; ?>><a href="<?php echo $path; ?>?order_by=recommended">Recommended</a></li>
	<li <?php if ($order_by == 'most_viewed') echo "class=\"filter-active\""; ?>><a href="<?php echo $path; ?>?order_by=most_viewed">Most Viewed</a></li>
	<li <?php if ($order_by == 'highest_rated') echo "class=\"filter-active\""; ?>><a href="<?php echo $path; ?>?order_by=highest_rated">Highest Rated</a></li>
</ul>
<br />		
        

		<?php
		
		if ( $order_by == 'recommended' ) {

			$args = array(
				'posts_per_page' => $show_posts,
				'paged' => $paged,
				'post_type' => $post_type,
				'tax_query' => array(
					'relation' => 'AND',
					array(
						'taxonomy' => $taxonomy,
						'field' => 'slug',
						'terms' => array( $category )
					),
					array(
						'taxonomy' => $taxonomy,
						'field' => 'slug',
						'terms' => array( 'recommended' )
					)
				),
				'post_status' => 'publish',
				'order' => 'DESC',
			);
			$wp_query = new WP_Query( $args );
			
		} elseif ( $order_by == 'highest_rated' ) {
			
			$wp_query = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $show_posts, 'paged' => $paged, 'post_type' => array($post_type), $taxonomy => $category, 'post_status' => 'publish', 'r_sortby'=>'highest_rated' ) ) );
			
		} elseif ( $order_by == 'most_viewed' ) {
			
			global $wpdb;
			
			$category_id = $wpdb->get_var("SELECT ".$wpdb->prefix."term_taxonomy.term_taxonomy_id FROM ".$wpdb->prefix."term_taxonomy LEFT JOIN ".$wpdb->prefix."terms ON ".$wpdb->prefix."term_taxonomy.term_id = ".$wpdb->prefix."terms.term_id WHERE ".$wpdb->prefix."terms.slug = '". $category ."'");
			
			$querystr = "SELECT DISTINCT wp_posts.* FROM wp_posts LEFT JOIN wp_popularpostsdata ON wp_posts.ID = wp_popularpostsdata.postid LEFT JOIN wp_term_relationships ON wp_posts.ID = wp_term_relationships.object_id WHERE wp_posts.post_type = '" . $post_type . "' AND wp_posts.post_status = 'publish' AND wp_term_relationships.term_taxonomy_id = " . $category_id . " ORDER BY wp_popularpostsdata.pageviews DESC LIMIT ". $showposts . " OFFSET " . $offset;	
							
		} else {
			
			// Latest
			$wp_query = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $show_posts, 'paged' => $paged, 'post_type' => array($post_type), $taxonomy => $category, 'orderby' => $order_by, 'order' => 'DESC', 'post_status' => 'publish' ) ) );
			
		}
		
		if (isset($querystr)) {
			global $wpdb;
			$pageposts = $wpdb->get_results($querystr, OBJECT);
	 
			if ($pageposts) {
				global $post;
				foreach ($pageposts as $post) { 
					setup_postdata($post);
					if ($post_type == 'video') {
						LTV_Display_Videos($post, $post_type);
					} else { 
						echo "<table width=\"633px\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"archive\"><tr>";
						LTV_Display_Posts($post, $post_type);
						echo "</tr></table>";
					}
				}
			} else { 
				echo "<p>No items found.</p>";
			}	 
		} elseif ($wp_query->have_posts()) {
				LTV_Display_Items($wp_query, $post_type);
			wp_reset_postdata();
		} else { 
			echo "<p>No items found.</p>";
	}	

	return;
}

function LTV_Display_Items($wp_query, $post_type) {
	if ($post_type == 'video') {
		if ($wp_query->have_posts()) {
			while ( $wp_query->have_posts() ) { 
				$wp_query->the_post();
				LTV_Display_Videos();
			}
		} else {
			echo "<p>No items found.</p>";
		}
	} else {
		if ($wp_query->have_posts()) {
			echo "<table width=\"633px\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"archive\"><tr>";
			while ( $wp_query->have_posts() ) { 
				$wp_query->the_post();
				LTV_Display_Posts();
			}
			echo "</tr></table>";
		} else {
			echo "<p>No items found.</p>";
		}
	}
}

function LTV_Display_Posts() {
	echo "<tr><td>";                   
    if ( has_post_thumbnail() ) { ?>
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('img-s'); ?></a>
	<?php 
	}
    echo "</td><td>"; ?>           
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <p class="meta">Posted by <?php the_author_posts_link(); ?> on <?php the_time("l, d.m.Y"); ?></p>
    <?php the_excerpt(); 
	echo "</td></tr>"; 	
         
}

function LTV_Display_Videos() {
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