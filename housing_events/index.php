<?php 
	// load WordPress header
	get_header();
	
	// query for all staff files
	query_posts(array('post_type' => array('event')));
	
	// set the post counter
	$post_counter = 0; 
	
	$post_id = $post->ID;
 	$move_type = get_post_meta($post_id, 'move_type', true);
	
	while (have_posts()) : the_post(); // begin page content
		$post_counter++; 
		?>
		<div>
			<h2><?php echo $move_type ?></h2>
			<p><?php echo the_content(); ?></p>
		</div>
		<?php 
		if($post_counter < $wp_query->found_posts){ echo '<hr/>';}
	endwhile; // end page content
	
	// end query
	wp_reset_query();
	
	// load WordPress footer
	get_footer(); 
?>