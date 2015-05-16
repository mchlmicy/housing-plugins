<?php
	// print the editbox content
	function mapsengine_inner_edit_box($post) 
	{
		// enqueue scripts
		wp_enqueue_script('jquery');
		wp_enqueue_script('housing_mapsengine_editboxJS');
		
		// enqueue styles
		wp_enqueue_style('housing_editboxCSS');
		
		// add a nonce field so we can check for it later.
  		wp_nonce_field(plugin_basename(__FILE__), 'mapsengine_inner_edit_box_nonce');
		
		// post variables
		global $post;
		
		function validate_mapsengine_plugin($num_maps, $post)
		{
			if(!is_int($num_maps)){echo 'Please select a valid number of maps.'; return;}
			else 
			{
				for($x = 1; $x <= $num_maps; $x++)
				{
					if(!get_post_meta($post->ID, 'map-'.$x.'-title', true) || !get_post_meta($post->ID, 'map-'.$x.'-url', true))
					{
						echo 'One or more of your maps are missing information.'; return;
					}
				}
			}
		}
		
		?>
       	<div>
        	<div style="margin-bottom: 9px"><strong style="color: #804040"><?php validate_mapsengine_plugin(intval(get_post_meta($post->ID, 'num_maps', true)), $post); ?></strong></div>
        	<div style="display: inline-block; margin: 4.5px 0px; width: 25%"><strong>Maps</strong></div>
            <select style="margin: 0px 4.5px 4.5px 12px" name="num_maps" onchange="selectMaps(jQuery(this).val())">
				<option>Number of maps</option>
				<?php
					for($x = 1; $x <= 4; $x++)
					{
						if($x != get_post_meta($post->ID, 'num_maps', true)){echo '<option value="'.$x.'">'.$x.'</option>';}
						else{echo '<option value="'.$x.'" selected>'.$x.'</option>';} 
					}
				?>
          	</select>
        </div>
        <div id="maps-tab-container" class="media-tab-container"></div>
        <div class="maps-container">
        	<?php
                for($x = 1; $x <= 4; $x++)
                {
                    ?>
                    <div id="map-<?php echo $x; ?>" style="display: none;">
                        <div style="margin-bottom: 12px">
                            <strong style="font-size: 15px">Map <?php echo $x; ?></strong>
                        </div>
                        <div style="margin-bottom: 12px">
                            <div style="margin-bottom: 6px">
                                <div style="display: inline-block; width: 25%"><strong>Title</strong></div>
                                <input type="text" style="margin: 0px 4.5px 4.5px 12px" name="map-<?php echo $x; ?>-title" value="<?php echo get_post_meta($post->ID, 'map-'.$x.'-title', true); ?>" placeholder="Title">
                            </div>
                            <div style="margin-bottom: 6px">
                                <div style="display: inline-block; width: 25%"><strong>URL</strong></div>
                                <input type="text" style="margin: 0px 4.5px 4.5px 12px" name="map-<?php echo $x; ?>-url" value="<?php echo get_post_meta($post->ID, 'map-'.$x.'-url', true); ?>" placeholder="Mapsengine Embed URL">
                            </div>
                        </div>
                    </div>
                    <?php
                } 
            ?>
        </div>
        <?php
    }

	// save box content
	function mapsengine_save_data($id) 
	{
		// verify nonce is set
		if(!wp_verify_nonce($_POST['mapsengine_inner_edit_box_nonce'], plugin_basename(__FILE__))){return $id;}
	  
		// if autosave...
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return $id;} 
	  
		// check permissions of user submitting
		if('page'==$_POST['post_type']) 
		{
	  			 if(!current_user_can('edit_page', $id)){return $id;} 
	 		else if(!current_user_can('edit_page', $id)){return $id;} 
		}
	
		// save
		$num_maps = intval($_POST['num_maps']); add_post_meta($id, 'num_maps', $num_maps); update_post_meta($id, 'num_maps', $num_maps);
		$map_fields = array('title','url');
		if(is_int($num_maps))
		{
			for($x = 1; $x <= 4; $x++)
			{
				if($x <= $num_maps)
				{
					// create meta fields if they don't exist
					for($y=0; $y<count($map_fields); $y++){if(!get_post_meta($id, 'map-'.$x.'-'.$map_fields[$y], true)){add_post_meta($id, 'map-'.$x.'-'.$map_fields[$y], $data);}}
					
					// update meta fields 
					for($y=0; $y<count($map_fields); $y++){update_post_meta($id, 'map-'.$x.'-'.$map_fields[$y], $_POST['map-'.$x.'-'.$map_fields[$y]]);}
				}
				else
				{
					// remove unused meta fields
					for($y=0; $y<count($map_fields); $y++){if(get_post_meta($id, 'map-'.$x.'-'.$map_fields[$y], true)){delete_post_meta($id, 'map-'.$x.'-'.$map_fields[$y], $data);}}
				}
			}
		}
	}
	add_action('save_post', 'mapsengine_save_data');

	// maps engine widget class
	class mapsengine extends WP_Widget 
	{
		// constructor
    	function mapsengine(){parent::WP_Widget(false, $name = __('Maps Engine', 'wp_widget_plugin'));}

		// display widget
		function widget($args, $instance) 
		{
			// enqueue scripts
			wp_enqueue_script('housing_bootstrapJS');
			wp_enqueue_script('housing_modalcustomJS');
			wp_enqueue_script('jquery');
			
			// enqueue styles
			wp_enqueue_style('housing_bootstrapCSS');
			wp_enqueue_style('housing_moduleCSS');
			wp_enqueue_style('housing_modalcustomCSS');
			wp_enqueue_style('housing_modalcustom_mapsengineCSS');
			
			// post variables
			global $post;
			$num_maps = intval(get_post_meta($post->ID, 'num_maps', true));
			
			function validate_mapsengine_plugin($num_maps, $post)
			{
				if(!is_int($num_maps) || $num_maps < 1){return false;}
				else 
				{
					for($x = 1; $x <= $num_maps; $x++)
					{
						if(!get_post_meta($post->ID, 'map-'.$x.'-title', true) || !get_post_meta($post->ID, 'map-'.$x.'-url', true))
						{
							return false;
						}
					} 
					return true;
				}
			}
			
			if(validate_mapsengine_plugin($num_maps, $post))
			{
				?>
					<div class="media-module">
						<div class="header"><h3><?php echo get_the_title(); ?> Maps</h3></div>
						<div class="body gallery">
                            <ul class="thumbnails">
								<?php
                                  	for($x = 1; $x <= $num_maps; $x++)
                                    {
                                      	?><li class="thumbnail-container"><a class="thumbnail modal-custom" data-mapsengine="maps" data-pagetitle="<?php echo get_the_title(); ?>" href="<?php echo get_post_meta($post->ID, 'map-'.$x.'-url', true); ?>" title="<?php echo get_post_meta($post->ID, 'map-'.$x.'-title', true); ?>"><img src="<?php echo plugins_url('../img/map_preview.png', __FILE__); ?>" alt="<?php echo get_post_meta($post->ID, 'map-'.$x.'-title', true); ?>"/></a></li><?php
                                    }
                                ?>
							</ul>
						</div>	
					</div>
				<?php
			}
		}
	}	
	add_action('widgets_init', create_function('', 'return register_widget("mapsengine");'));
?>