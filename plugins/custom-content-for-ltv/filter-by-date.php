<?php 

/* Filter content by date, works for both posts and videos */

function LTV_Filter_By_Date( $cat ) {
	
	if (isset($_GET['display']) and $_GET['display'] != '') {
		$display = $_GET['display'];
	} else {
		$display = 'all';
	}
	
	global $wp_query;

	$showposts = get_query_var('posts_per_page');
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	
	$post_type = get_post_type();
	
	if ($post_type == 'post') {
		// get path
		$category_id = get_category(get_query_var('cat'),false)->term_id; // get current category id
		$category_parent_id = get_category(get_query_var('cat'),false)->category_parent;
		$category_parent = get_category($category_parent_id)->slug; // e.g. news
		$current_category = get_category($category_id)->slug; // e.g. politics
		$url = home_url()."/category/".$category_parent."/".$current_category."/";
	} else {
		$taxonomy = get_query_var($wp_query->query_vars['taxonomy']);
		$tax_parent = $wp_query->tax_query->queries[0]['taxonomy']; 
		$url = home_url()."/".$tax_parent."/".$taxonomy."/";
	}
			
?>

<ul class="filter">
	<li <?php if ($display == 'all') echo "class=\"filter-active\""; ?>><a href="<?php echo $url; ?>?display=all">All</a></li>
	<li <?php if ($display == 'today') echo "class=\"filter-active\""; ?>><a href="<?php echo $url; ?>?display=today">Today</a></li>
	<li <?php if ($display == 'yesterday') echo "class=\"filter-active\""; ?>><a href="<?php echo $url; ?>?display=yesterday">Yesterday</a></li>
	<li <?php if ($display == 'this-week') echo "class=\"filter-active\""; ?>><a href="<?php echo $url; ?>?display=this-week">This Week</a></li>
	<li <?php if ($display == 'last-week') echo "class=\"filter-active\""; ?>><a href="<?php echo $url; ?>?display=last-week">Last Week</a></li>
</ul>
<br />
      
<?php

	$today = getdate();

	global $wp_query;

	// get posts
	if($post_type == 'post' && $display != 'all') {

		if ($display == 'today') {
			// today only
			$args = array(
				'category_name' => $current_category,
				'year' => $today["year"],
				'monthnum' => $today["mon"],
				'day' => $today["mday"],
				'posts_per_page' => $show_posts,
				'paged' => $paged,
				'post_status' => 'publish'
			);
		} elseif ($display == 'yesterday') {
			// yesterday only
			$args = array(
				'category_name' => $current_category,
				'year' => $today["year"],
				'monthnum' => $today["mon"],
				'day' => $today["mday"]-1,
				'posts_per_page' => $show_posts,
				'paged' => $paged,
				'post_status' => 'publish'
			);
		} elseif ($display == 'this-week') {
			// this week
			$args = array(
				'category_name' => $current_category,
				'year' => date('Y'),
				'w' => date('W'),
				'posts_per_page' => $show_posts,
				'paged' => $paged,
				'post_status' => 'publish'
			);
		} elseif ($display == 'last-week') {
			// last week
			$args = array(
				'category_name' => $current_category,
				'year' => date('Y'),
				'w' => date('W')-1,
				'posts_per_page' => $show_posts,
				'paged' => $paged,
				'post_status' => 'publish'
			);
		}

	// get videos
	} elseif (isset($taxonomy)) {

		if ($display == 'today') {
			// today only
			$args = array(
				'post_type' => 'video',
				'video_category' => $taxonomy,
				'year' => $today["year"],
				'monthnum' => $today["mon"],
				'day' => $today["mday"],
				'posts_per_page' => $show_posts,
				'paged' => $paged,
				'post_status' => 'publish'
			);
		} elseif ($display == 'yesterday') {
			// yesterday only
			$args = array(
				'post_type' => 'video',
				'video_category' => $taxonomy,
				'year' => $today["year"],
				'monthnum' => $today["mon"],
				'day' => $today["mday"]-1,
				'posts_per_page' => $show_posts,
				'paged' => $paged,
				'post_status' => 'publish'
			);
		} elseif ($display == 'this-week') {
			// this week
			$args = array(
				'post_type' => 'video',
				'video_category' => $taxonomy,
				'year' => date('Y'),
				'w' => date('W'),
				'posts_per_page' => $show_posts,
				'paged' => $paged,
				'post_status' => 'publish'
			);
		} elseif ($display == 'last-week') {
			// last week
			$args = array(
				'post_type' => 'video',
				'video_category' => $taxonomy,
				'year' => date('Y'),
				'w' => date('W')-1,
				'posts_per_page' => $show_posts,
				'paged' => $paged,
				'post_status' => 'publish'
			);
		// show all except latest
		} else {
			if ($taxonomy == 'video-reports') {
				$args = array(
					'post_type' => 'video',
					'video_category' => $taxonomy,
					'offset' => 0,
					'posts_per_page' => $show_posts,
					'paged' => $paged,
					'post_status' => 'publish'
				);
			} else {
				$offset = ($showposts * ( $paged-1 ))+1;
				$totalposts = $wp_query->found_posts - 1; // needed for correct pagination
				global $totalpages;
				if(isset($totalposts)) $totalpages = $totalposts / get_query_var('posts_per_page');
				$args = array(
					'post_type' => 'video',
					'video_category' => $taxonomy,
					'offset' => $offset,
					'posts_per_page' => $show_posts,
					'paged' => $paged,
					'post_status' => 'publish'
				);
			}
		}

	}

	if(isset($args)) $wp_query = new WP_Query( $args );

	return $wp_query;

	}

?>