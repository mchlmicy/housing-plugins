<?php 
	// load WordPress header
	get_header();
	
	// query for all staff files
	query_posts(array('post_type' => array('contact')));
	
	//Register Styles
	wp_enqueue_style('contactCSS', plugins_url('/css/contacts.css', __FILE__), false, '1.0.0', 'all');
	wp_register_style('contactCSS');
	
	// set the post counter
	$post_counter = 0; 
	while (have_posts()) : the_post(); // begin page content
		if($post_counter==0){echo '<div class="contact-row">';}
		$post_counter++;
		global $post;
		$job_title = get_post_meta($post->ID, 'contact_job_title', true);
	 	$phone = get_post_meta($post->ID, 'contact_phone', true);
 		$email = get_post_meta($post->ID, 'contact_email', true);
 		$office = get_post_meta($post->ID, 'contact_office', true);
		$thumb_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'thumbnail') );
		?>
		<div class="contact archive">
        	<div class="photo-frame"><?php if($thumb_url){ ?><img src="<?php echo $thumb_url ?>" /><?php } ?></div>
			<div class="contact-content">
            	<div class="container">
                    <div class="sub-header"><?php echo $job_title; ?></div>
                    <div class="header"><?php the_title(); ?></div>
                    <div class="header-link"><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></div>
                    <div class="body"><?php the_content(); ?></div>
                    <?php if($post->post_content!=""){echo '<hr/>';} ?>
                    <div class="footer-link"><?php echo $phone; ?></div>
                    <div class="footer-link"><?php echo $office; ?></div>
				</div>
			</div>
		</div>
		<?php 
		if($post_counter!=0 && $post_counter % 3 == 0){echo '</div>'; if($post_counter < $wp_query->found_posts){echo '<div class="contact-row">';}}
		else if($post_counter == $wp_query->found_posts){echo '</div>';}
	endwhile; // end page content
	
	// end query
	wp_reset_query();
	
	// load WordPress footer
	get_footer(); 
?>