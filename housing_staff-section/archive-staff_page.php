<?php 
	// allow logout
	if(isset($_REQUEST['logout'])) 
	{
		phpCAS::logout();
	}
	
	// load WordPress header
	get_header();
	
	// query for all staff files
	query_posts(array('post_type' => array('staff_page')));
	
	// register & enque styles
	wp_register_style('housingStaff_CSS', plugins_url('/css/housing_staff.css', __FILE__), false, '1.0.0', 'all');
	wp_enqueue_style('housingStaff_CSS');
	
	$post_counter = 0; 
	
	function is_photo($file_url)
	{
		$img_ext = array('.png', '.jpg', '.gif', '.tif');
		$is_photo = false;
		
		for($x = 0; $x < count($img_ext); $x++)
		{
			if(strpos($file_url, $img_ext[$x])>0)
			{
				$is_photo = true;
				return $is_photo;
			}
		}
		
		return $is_photo;
	}
	 
	// begin page content
	?>
    <div class="housing-staff-files">
    	<?php
			while (have_posts()) : the_post();
				$post_counter++; 
				?>
				<div class="file-container">
					<div class="thumbnail-container">
						<?php 
							$url = get_post_meta( $post->ID, 'staff_file_upload', true );
						
							if(is_photo($url['url']) == true)
							{
								echo '<img class="thumbnail" src="'.$url['url'].'">';
							}
							else
							{
								echo '<img class="stock-thumbnail" src="'.plugins_url("images/file_icon.png", __FILE__ ).'" height="60px">';
							}
						?>	
					</div>
					<div class="description-container">
						<div class="title"><?php $post_id = $post->ID; ?><a href="<?php get_permalink(); ?>"><?php the_title(); ?></a></div>
						<div class="description"><a class="file-download" href="<?php $url = get_post_meta( $post->ID, 'staff_file_upload', true ); echo $url['url']; ?>">View File</a><?php the_content(); ?></div>
					</div>
				</div>
				<?php
				// end page content
			endwhile;
		?>
    </div>
    <?php 
	
	// end query
	wp_reset_query();
	
	// load WordPress footer
	get_footer(); 
?>