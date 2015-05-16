<?php
	// load WordPress header
	get_header();
	global $post;
	$page_id = $post->ID;
	
	echo $post->post_content;
	
	// reset WordPress query
	wp_reset_query();
	
	// load WordPress footer
	get_footer();
	

?>