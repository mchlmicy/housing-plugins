<?php
	// lightbox gallery widget class
	class lightboxgallery extends WP_Widget 
	{
		// constructor
    	function lightboxgallery() 
		{
        	parent::WP_Widget(false, $name = __('Lightbox Gallery', 'wp_widget_plugin') );
		}

		// wisplay widget
		function widget($args, $instance) 
		{
			// enqueue Styles
			wp_enqueue_style('housing_bootstrapCSS');
			wp_enqueue_style('housing_modalcustomCSS');
			wp_enqueue_style('housing_modalcustom_lightboxCSS');
			wp_enqueue_style('housing_moduleCSS');
			
			
			// enueue Scripts
			wp_enqueue_script('housing_bootstrapJS'); 
			wp_enqueue_script('housing_modalcustomJS'); 
			wp_enqueue_script('jquery');
			
            //Variables
			$post_id = $GLOBALS['post']->ID;
			$num_photos_value = get_post_meta($post_id, 'num_photos', true);
		
			//Check if there are any floorplans or photos or not and set display of the outputs
			if(get_post_meta($post_id, 'num_floorplans', true)!=0)
			{
				$page_title = get_the_title();
				
				echo '	<div class="media-module" style="margin-bottom: 19px">
							<div class="header"><h3>Floorplans</h3></div>
							<div class="body gallery">';
				
				for ($i = 1; $i <= get_post_meta($post_id, 'num_floorplans', true); $i++) 
				{
					if($i<10){$i_num = "floorplan_0".$i;} else{$i_num = "floorplan_".$i;}
											
					$attachment_id = get_post_meta($post_id, $i_num, true);
					$alt = get_the_title($attachment_id);
					$caption = get_post_field('post_excerpt', $attachment_id);
					$src = get_field($i_num);
					$thumb = $src['sizes']['thumbnail'];
					$large = $src['sizes']['large'];
				
					// if a blank floorplan, break for loop
					if($attachment_id==null){$i = get_post_meta($post_id, 'num_floorplans', true);}
					else
					{	
						// if this is the first thumbnail in the row...
						if($i%4==1){echo '<ul class="thumbnails">';}
																
						echo '<li class="thumbnail-container"><a class="thumbnail modal-custom" data-lightbox="floorplans" data-pagetitle="'.esc_attr($page_title).'" href="'.esc_attr($large).'" title="'.esc_attr($caption).'"><img src="'.esc_attr($thumb).'" alt="'.esc_attr($alt).'"/></a></li>';
					
						// if this is the last thumbnail in the row...
						if($i%4==0 || $i==get_post_meta($post_id, 'num_floorplans', true)){echo '</ul>';}
					}
				}
				
				echo '		</div>
						</div>';
			} 
			
			if(get_post_meta($post_id, 'num_photos', true)!=0)
			{
				$page_title = get_the_title();
				
				echo '	<div class="media-module" style="margin-bottom: 19px">
							<div class="header"><h3>Photos</h3></div>
							<div class="body gallery">';
				
				for ($i = 1; $i <= get_post_meta($post_id, 'num_photos', true); $i++) 
				{
					if($i<10){$i_num = "image_0".$i;} else{$i_num = "image_".$i;}
											
					$attachment_id = get_post_meta($post_id, $i_num, true);
					$alt = get_the_title($attachment_id);
					$caption = get_post_field('post_excerpt', $attachment_id);
					$src = get_field($i_num);
					$thumb = $src['sizes']['thumbnail'];
					$large = $src['sizes']['large'];
												
					//If a blank photo, break for loop
					if($attachment_id==null){$i = get_post_meta($post_id, 'num_photos', true);}
					else
					{	
						// if this is the first thumbnail in the row...
						if($i%4==1){echo '<ul class="thumbnails">';}
																
						echo '<li class="thumbnail-container"><a class="thumbnail modal-custom" data-lightbox="photos" data-pagetitle="'.esc_attr($page_title).'" href="'.esc_attr($large).'" title="'.esc_attr($caption).'"><img src="'.esc_attr($thumb).'" alt="'.esc_attr($alt).'"/></a></li>';
						
						// if this is the last thumbnail in the row...
						if($i%4==0 || $i==get_post_meta($post_id, 'num_photos', true)){echo '</ul>';}
					}
				}
					
				echo '		</div>
						</div>';
			} 
		}
	}	
	add_action('widgets_init', create_function('', 'return register_widget("lightboxgallery");'));
?>