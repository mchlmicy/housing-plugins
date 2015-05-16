<?php 
	// load WordPress header
	get_header();
		
	global $post;
	$post_id = $post->ID;
	$job_title = get_post_meta($post_id, 'contact_job_title', true);
 	$phone = get_post_meta($post_id, 'contact_phone', true);
 	$email = get_post_meta($post_id, 'contact_email', true);
 	$office = get_post_meta($post_id, 'contact_office', true);
		?>
		<div>
			<h2><?php the_title(); ?></h2>
			<p><?php echo $job_title; ?></p>
			<p><?php echo $phone; ?></p>
            <p><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>
			<p><?php echo $office; ?></p>
		</div>
		<?php 
	// end query
	wp_reset_query();
	
	// load WordPress footer
	get_footer(); 
?>