<?php
	// print the editbox content
	function videos_inner_edit_box($post) 
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('housing_videos_editboxJS'); 
		wp_enqueue_style('housing_editboxCSS');
		
		// add a nonce field so we can check for it later.
  		wp_nonce_field(plugin_basename(__FILE__), 'videos_inner_edit_box_nonce');
		
		// post variables
		global $post;
		
		function validate_videos_plugin($num_videos, $post){if(!is_int($num_videos)){echo 'Please select a valid number of videos.'; return;} else{for($x = 1; $x <= $num_videos; $x++){if(!get_post_meta($post->ID, 'video-'.$x.'-title', true) || !get_post_meta($post->ID, 'video-'.$x.'-url', true)){echo 'One or more of your videos are missing information.'; return;}}}}
		
		?>
       	<div>
        	<div style="margin-bottom: 9px"><strong style="color: #804040"><?php validate_videos_plugin(intval(get_post_meta($post->ID, 'num_videos', true)), $post); ?></strong></div>
        	<div style="display: inline-block; margin: 4.5px 0px; width: 25%"><strong>Videos</strong></div>
            <select style="margin: 0px 4.5px 4.5px 12px" name="num_videos" onchange="selectVideos(jQuery(this).val())">
				<option>Number of videos</option>
				<?php
					for($x = 1; $x <= 4; $x++)
					{
						if($x != get_post_meta($post->ID, 'num_videos', true)){echo '<option value="'.$x.'">'.$x.'</option>';}
						else{echo '<option value="'.$x.'" selected>'.$x.'</option>';} 
					}
				?>
          	</select>
        </div>
        <div class="media-tab-container videos"></div>
        <div class="media-container videos">
        	<?php
                for($x = 1; $x <= 4; $x++)
                {
                    ?>
                    <div id="video-<?php echo $x; ?>" style="display: none;">
                        <div style="margin-bottom: 12px"><strong style="font-size: 15px">Video <?php echo $x; ?></strong></div>
                        <div style="margin-bottom: 12px">
                            <div style="margin-bottom: 6px"><div style="display: inline-block; width: 25%"><strong>Title</strong></div><input type="text" style="margin: 0px 4.5px 4.5px 12px" name="video-<?php echo $x; ?>-title" value="<?php echo get_post_meta($post->ID, 'video-'.$x.'-title', true); ?>" placeholder="Title"></div>
                            <div style="margin-bottom: 6px"><div style="display: inline-block; width: 25%"><strong>URL</strong></div><input type="text" style="margin: 0px 4.5px 4.5px 12px" name="video-<?php echo $x; ?>-url" value="<?php echo get_post_meta($post->ID, 'video-'.$x.'-url', true); ?>" placeholder="Videos Embed URL"></div>
                        </div>
                    </div>
                    <?php
                } 
            ?>
        </div>
        <?php
    }

	// save box content
	function videos_save_data($id) 
	{
		// verify nonce is set
		if(!wp_verify_nonce($_POST['videos_inner_edit_box_nonce'], plugin_basename(__FILE__))){return $id;}
	  
		// if autosave...
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return $id;} 
	  
		// check permissions of user submitting
		if('page'==$_POST['post_type']) 
		{
	  			 if(!current_user_can('edit_page', $id)){return $id;} 
	 		else if(!current_user_can('edit_page', $id)){return $id;} 
		}
	
		// save
		$num_videos = intval($_POST['num_videos']); add_post_meta($id, 'num_videos', $num_videos); update_post_meta($id, 'num_videos', $num_videos);
		$video_fields = array('title','url');
		if(is_int($num_videos))
		{
			for($x = 1; $x <= 4; $x++)
			{
				if($x <= $num_videos)
				{
					// create meta fields if they don't exist
					for($y=0; $y<count($video_fields); $y++){if(!get_post_meta($id, 'video-'.$x.'-'.$video_fields[$y], true)){add_post_meta($id, 'video-'.$x.'-'.$video_fields[$y], $data);}}
					
					// update meta fields 
					for($y=0; $y<count($video_fields); $y++){update_post_meta($id, 'video-'.$x.'-'.$video_fields[$y], $_POST['video-'.$x.'-'.$video_fields[$y]]);}
				}
				else
				{
					// remove unused meta fields
					for($y=0; $y<count($video_fields); $y++){if(get_post_meta($id, 'video-'.$x.'-'.$video_fields[$y], true)){delete_post_meta($id, 'video-'.$x.'-'.$video_fields[$y], $data);}}
				}
			}
		}
	}
	add_action('save_post', 'videos_save_data');

	// videos widget class
	class videos extends WP_Widget 
	{
		// constructor
    	function videos(){parent::WP_Widget(false, $name = __('Videos', 'wp_widget_plugin'));}

		// display widget
		function widget($args, $instance) 
		{
			// enqueue scripts
			wp_enqueue_script('jquery');
			wp_enqueue_script('housing_bootstrapJS');
			wp_enqueue_script('housing_modalcustomJS');
			
			// enqueue styles
			wp_enqueue_style('housing_bootstrapCSS');
			wp_enqueue_style('housing_moduleCSS');
			wp_enqueue_style('housing_modalcustomCSS');
			
			// post variables
			global $post;
			$page_title = get_the_title();
			$num_videos = intval(get_post_meta($post->ID, 'num_videos', true));
			
			function validate_videos_plugin($num_videos, $post){if(!is_int($num_videos) || $num_videos < 1){return false;} else{for($x = 1; $x <= $num_videos; $x++){if(!get_post_meta($post->ID, 'video-'.$x.'-title', true) || !get_post_meta($post->ID, 'video-'.$x.'-url', true)){return false;}} return true;}}
			
			if(validate_videos_plugin($num_videos, $post))
			{
				?>
					<div class="media-module">
						<div class="header"><h3><?php echo get_the_title(); ?> Videos</h3></div>
						<div class="body gallery">
                            <ul class="thumbnails">
								<?php
                                  	for($x = 1; $x <= $num_videos; $x++)
                                    {
                                      	?><li class="thumbnail-container"><a class="thumbnail modal-custom" data-videos="videos" data-pagetitle="<?php echo esc_attr($page_title); ?>" href="<?php echo get_post_meta($post->ID, 'video-'.$x.'-url', true); ?>" title="<?php echo get_post_meta($post->ID, 'video-'.$x.'-title', true); ?>"><img src="<?php echo plugins_url('../img/video_preview.png', __FILE__); ?>" alt="<?php echo get_post_meta($post->ID, 'video-'.$x.'-title', true); ?>"/></a></li><?php
                                    }
                                ?>
							</ul>
						</div>	
					</div>
				<?php
			}
		}
	}	
	add_action('widgets_init', create_function('', 'return register_widget("videos");'));
?>