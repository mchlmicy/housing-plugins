<?php
	function update_edit_form() 
	{
    	echo ' enctype="multipart/form-data"';
	} 
	add_action('post_edit_form_tag', 'update_edit_form');

	function custom_upload($mimes)
	{
		$mimes = array_merge($mimes, array('xml' => 'application/xml','swf' => 'application/x-shockwave-flash'));
		return $mimes;
	}
	add_filter('upload_mimes', 'custom_upload');
	
	//Prints the box content
	function panoramas_inner_edit_box($post) 
	{
		//Queue Scripts
		wp_enqueue_script('housing_panorama_editboxJS');
		wp_enqueue_script('jquery');
		
		//Queue Styles
		wp_enqueue_style('housing_editboxCSS');
		
		//Add an nonce field so we can check for it later.
  		wp_nonce_field(plugin_basename(__FILE__), 'panoramas_inner_edit_box_nonce' );
		
		?>
       	<div>
        	<div style="display: inline-block; margin: 4.5px 0px; width: 25%"><strong>Panoramas</strong></div>
            <select style="margin: 0px 4.5px 4.5px 12px" name="num_panoramas" onchange="selectPanoramas(jQuery(this).val())">
				<?php
					$x = 1;
					while($x <= 4)
					{
						if($x == 1){echo '<option value="null">Number of panoramas</option>';}?>
                        <option value="<?php echo $x; ?>" <?php if($x==get_post_meta( $post->ID, 'num_panoramas', true )){echo 'selected';}?>><?php echo $x; ?></option>
						<?php 
						$x++;
					}
				?>
          	</select>
        </div>
        <div id="panorama-tab-container" class="media-tab-container"></div>
        <div class="media-container pano">
		<?php
			
		$x = 1;
		while($x <=4)
		{
			$title = get_post_meta( $post->ID, 'panorama-'.$x.'-title', true );
			$description = get_post_meta( $post->ID, 'panorama-'.$x.'-description', true );
			?>
            <div id="panorama-<?php echo $x; ?>" style="display: none">
				<div style="margin-bottom: 6px">
                	<strong style="font-size: 15px">Panorama <?php echo $x; ?></strong>
                </div>
                <div style="margin-bottom: 12px">
                    <div style="margin-bottom: 4.5px">
                        <div style="vertical-align: top; width: 25%"><strong>Panorama</strong></div>
                    </div>
                    <?php if(get_post_meta( $post->ID, 'panorama-'.$x.'-preview', true )){ ?>
                    <div style="margin-bottom: 6px">
                    	<div class="panorama-container"><img src="<?php $img = get_post_meta( $post->ID, 'panorama-'.$x.'-preview', true ); echo $img['url']; ?>" style="width:100%" /></div>
                    </div>
                    <?php } ?>
                    <div>
                        <div style="display: inline-block; width: 25%"><strong style="margin-left: 19px">Title</strong></div>
                        <input type="text" style="margin: 0px 4.5px 4.5px 12px" name="panorama-<?php echo $x; ?>-title" value="<?php echo esc_attr($title); ?>">
                    </div>
                    <div>
                        <div style="display: inline-block; width: 25%"><strong style="margin-left: 19px">Description</strong></div>
                        <input type="text" style="margin: 0px 4.5px 4.5px 12px" name="panorama-<?php echo $x; ?>-description" value="<?php echo esc_attr($description); ?>">
                    </div>
                    <?php if(get_post_meta( $post->ID, 'panorama-'.$x.'-preview', true)){ ?>
                    	<div id="panorama-<?php echo $x; ?>-preview-file">
                           	<div style="display: inline-block; width: 25%;"><strong style="margin-left: 19px">Preview Image</strong></div>
    	               		<div style="margin: 0px 4.5px 4.5px 12px;display:inline-block;"><p style="white-space: nowrap; text-overflow: ellipsis; display: inline; overflow: hidden;"><?php $file = get_post_meta( $post->ID, 'panorama-'.$x.'-preview', true ); echo basename($file['url']); ?></p><a class="x-new-upload" href="#" onclick="event.preventDefault();replaceExistingPanoImage('panorama-<?php echo $x; ?>-preview-file','panorama-<?php echo $x; ?>-preview-reveal');">&#x2716</a></div>
        	            </div>
                        <div  id="panorama-<?php echo $x; ?>-preview-reveal" style="display:none;">
                           	<div style="display: inline-block; width: 25%"><strong style="margin-left: 19px">Preview Image</strong></div>
                     		<input type="file" style="margin: 0px 4.5px 4.5px 12px" name="panorama-<?php echo $x; ?>-preview">
        	       		</div>
                    <?php } else { ?>
	                    <div>
    	                    <div style="display: inline-block; width: 25%"><strong style="margin-left: 19px">Preview Image</strong></div>
        	                <input type="file" style="margin: 0px 4.5px 4.5px 12px" name="panorama-<?php echo $x; ?>-preview" value="<?php esc_attr(get_post_meta( $post->ID, 'panorama-'.$x.'-preview', true )); ?>">
            	        </div>
                    <?php } ?>
                    <?php if(get_post_meta( $post->ID, 'panorama-'.$x.'-swf', true)){ ?>
                    	<div id="panorama-<?php echo $x; ?>-swf-file">
                           	<div style="display: inline-block; width: 25%;"><strong style="margin-left: 19px">SWF File</strong></div>
    	               		<div style="margin: 0px 4.5px 4.5px 12px;display:inline-block;"><p style="white-space: nowrap; text-overflow: ellipsis; display: inline; overflow: hidden;"><?php $file = get_post_meta( $post->ID, 'panorama-'.$x.'-swf', true ); echo basename($file['url']); ?></p><a class="x-new-upload" href="#" onclick="event.preventDefault();replaceExistingPanoImage('panorama-<?php echo $x; ?>-swf-file','panorama-<?php echo $x; ?>-swf-reveal');">&#x2716</a></div>
        	            </div>
                        <div  id="panorama-<?php echo $x; ?>-swf-reveal" style="display:none;">
                           	<div style="display: inline-block; width: 25%"><strong style="margin-left: 19px">SWF File</strong></div>
                     		<input type="file" style="margin: 0px 4.5px 4.5px 12px" name="panorama-<?php echo $x; ?>-swf">
        	       		</div>
                    <?php } else { ?>
	                    <div>
    	                    <div style="display: inline-block; width: 25%"><strong style="margin-left: 19px">SWF File</strong></div>
        	                <input type="file" style="margin: 0px 4.5px 4.5px 12px" name="panorama-<?php echo $x; ?>-swf">
            	        </div>
                     <?php } ?>
                </div>
                <div style="margin-bottom: 12px">
                    <div style="margin-bottom: 6px">
                        <div style="display: inline-block; width: 25%"><strong>HTML5 Support</strong></div> 
                        <span style="margin: 0px 4.5px 4.5px 12px"><input type="radio" id="panorama-<?php echo $x; ?>-html5" name="panorama-<?php echo $x; ?>-html5" value="Yes" <?php if(get_post_meta( $post->ID, 'panorama-'.$x.'-html5', true )=='Yes'){echo 'checked';} ?> onclick="revealHTML5Options(<?php echo $x; ?>, 'Yes')"> Yes</span>
                    	<span><input type="radio" id="panorama-<?php echo $x; ?>-html5" name="panorama-<?php echo $x; ?>-html5" value="No" <?php if(get_post_meta( $post->ID, 'panorama-'.$x.'-html5', true )=='No'){echo 'checked';} ?> onclick="revealHTML5Options(<?php echo $x; ?>, 'No')"> No</span>
                    </div>
                    <div id="panorama-<?php echo $x; ?>-html5-options" <?php if(!(get_post_meta( $post->ID, 'panorama-'.$x.'-html5', true )) || get_post_meta( $post->ID, 'panorama-'.$x.'-html5', true )=='No'){ ?>style="display:none"<?php } ?>>
                        <div style="margin-bottom: 6px">
                        	<?php if(get_post_meta( $post->ID, 'panorama-'.$x.'-xml', true)){ ?>
                    			<div id="panorama-<?php echo $x; ?>-xml-file">
                           			<div style="display: inline-block; width: 25%;"><strong style="margin-left: 19px">XML File</strong></div>
    	               				<div style="margin: 0px 4.5px 4.5px 12px;display:inline-block;"><p style="white-space: nowrap; text-overflow: ellipsis; display: inline; overflow: hidden;"><?php $file = get_post_meta( $post->ID, 'panorama-'.$x.'-xml', true ); echo basename($file['url']); ?></p><a class="x-new-upload" href="#" onclick="event.preventDefault();replaceExistingPanoImage('panorama-<?php echo $x; ?>-xml-file','panorama-<?php echo $x; ?>-xml-reveal');">&#x2716</a></div>
        	            		</div>
                        		<div  id="panorama-<?php echo $x; ?>-xml-reveal" style="display:none;">
                           			<div style="display: inline-block; width: 25%"><strong style="margin-left: 19px">XML File</strong></div>
                     				<input type="file" style="margin: 0px 4.5px 4.5px 12px" name="panorama-<?php echo $x; ?>-xml">
        	       				</div>
							<?php } else { ?>
                        		<div>
	                       			<div style="display: inline-block; width: 25%"><strong style="margin-left: 19px">XML File</strong></div>
    	                   			<input type="file" style="margin: 0px 4.5px 4.5px 12px" name="panorama-<?php echo $x; ?>-xml">
        	            		</div>
                            <?php } ?>
                        	<?php for($y = 1; $y <= 18; $y++){?>
                            	<?php if(get_post_meta( $post->ID, 'panorama-'.$x.'-image-'.$y, true)){ ?>
                                <div id="panorama-<?php echo $x; ?>-image-<?php echo $y; ?>-file">
                                	<div style="display: inline-block; width: 25%;"><strong style="margin-left: 19px">Image <?php echo $y; ?></strong></div>
    	                    		<div style="margin: 0px 4.5px 4.5px 12px;display:inline-block;"><p style="white-space: nowrap; text-overflow: ellipsis; display: inline; overflow: hidden;"><?php $file = get_post_meta( $post->ID, 'panorama-'.$x.'-image-'.$y, true ); echo basename($file['url']); ?></p><a class="x-new-upload" href="#" onclick="event.preventDefault();replaceExistingPanoImage('panorama-<?php echo $x; ?>-image-<?php echo $y; ?>-file','panorama-<?php echo $x; ?>-image-<?php echo $y; ?>-reveal');">&#x2716</a></div>
        	            		</div>
                                <div  id="panorama-<?php echo $x; ?>-image-<?php echo $y; ?>-reveal" style="display:none;">
                                	<div style="display: inline-block; width: 25%"><strong style="margin-left: 19px">Image <?php echo $y; ?></strong></div>
    	                    		<input type="file" style="margin: 0px 4.5px 4.5px 12px" name="panorama-<?php echo $x; ?>-image-<?php echo $y; ?>">
        	            		</div>
                                <?php } else { ?>
                                <div>
                                	<div style="display: inline-block; width: 25%"><strong style="margin-left: 19px">Image <?php echo $y; ?></strong></div>
    	                    		<input type="file" style="margin: 0px 4.5px 4.5px 12px" name="panorama-<?php echo $x; ?>-image-<?php echo $y; ?>">
        	            		</div>
                                <?php } ?>
                            <?php } ?>
                  		</div>
                	</div>
                </div>
            </div>
        <?php
			$x++;
		}
		?>
        </div>
        <?php
	}
	
	
	function panoramas_save_data($id) {

		/* --- security verification --- */
		if(!wp_verify_nonce($_POST['panoramas_inner_edit_box_nonce'], plugin_basename(__FILE__))) {
		  return $id;
		} // end if
	  
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		  return $id;
		} // end if
	  
		if('page' == $_POST['post_type']) {
			if(!current_user_can('edit_page', $id)) {
	    		return $id;
	  		} // end if
		} else {
   			if(!current_user_can('edit_page', $id)) {
		    	return $id;
		   	} // end if
		} // end if
		/* - end security verification - */
	
		// Determine the number of panoramas to save
		
		if(is_numeric($_POST['num_panoramas'])){
			$num_panoramas = $_POST['num_panoramas']; add_post_meta($id, 'num_panoramas', $num_panoramas); update_post_meta($id, 'num_panoramas', $num_panoramas);	
			for($x = 1; $x <= $num_panoramas; $x++)
			{
				// Save title
				if(!empty($_POST['panorama-'.$x.'-title'])){
					$data = $_POST['panorama-'.$x.'-title']; add_post_meta($id, 'panorama-'.$x.'-title', $data); update_post_meta($id, 'panorama-'.$x.'-title', $data);	
				}
				// Save description
				if(!empty($_POST['panorama-'.$x.'-description'])) {
					$data = $_POST['panorama-'.$x.'-description']; add_post_meta($id, 'panorama-'.$x.'-description', $data); update_post_meta($id, 'panorama-'.$x.'-description', $data);	
				}
	
				// Make sure the preview image file array isn't empty
				if(!empty($_FILES['panorama-'.$x.'-preview']['name'])) {
					// Check if the type is supported. If not, throw an error.
					$upload = $_FILES['panorama-'.$x.'-preview'];
					$type = wp_check_filetype($upload['name'], array('png' => 'image/png', 'jpg' => 'image/jpeg', 'gif' => 'image/gif'));		
					
					if($type['type']) {
						// Use the WordPress API to upload the file
						$upload = wp_handle_upload($upload, array( 'test_form' => false ));
						if(isset($upload['error']) && $upload['error'] != 0) {
							wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
						} else {
							add_post_meta($id, 'panorama-'.$x.'-preview', $upload); update_post_meta($id, 'panorama-'.$x.'-preview', $upload);		
						}
					} else {
						wp_die("The file type that you've uploaded is not an image file.");
					}	
				}
				
				// Make sure the SWF file array isn't empty
				if(!empty($_FILES['panorama-'.$x.'-swf']['name'])) {
					// Check if the type is supported. If not, throw an error.
					$upload = $_FILES['panorama-'.$x.'-swf'];
					$type = wp_check_filetype($upload['name'], array('swf' => 'application/x-shockwave-flash'));		
					
					if($type['type']) {
						// Use the WordPress API to upload the file
						$upload = wp_handle_upload($upload, array( 'test_form' => false ));
						if(isset($upload['error']) && $upload['error'] != 0) {
							wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
						} else {
							add_post_meta($id, 'panorama-'.$x.'-swf', $upload); update_post_meta($id, 'panorama-'.$x.'-swf', $upload);		
						}
					} else {
						wp_die("The file type that you've uploaded is not an SWF file.");
					}	
				}
				
				// Determine if HTML5 panoramas are supported
				if(!empty($_POST['panorama-'.$x.'-html5'])) {
					$data = $_POST['panorama-'.$x.'-html5']; add_post_meta($id, 'panorama-'.$x.'-html5', $data); update_post_meta($id, 'panorama-'.$x.'-html5', $data);	
				}
				
				// Make sure the XML file array isn't empty
				if(!empty($_FILES['panorama-'.$x.'-xml']['name'])) {
					// Check if the type is supported. If not, throw an error.
					$upload = $_FILES['panorama-'.$x.'-xml'];
					$type = wp_check_filetype($upload['name'], array('xml' => 'application/xml'));		
					
					if($type['type']) {
						// Use the WordPress API to upload the file
						$upload = wp_handle_upload($upload, array( 'test_form' => false ));
						if(isset($upload['error']) && $upload['error'] != 0) {
							wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
						} else {
							add_post_meta($id, 'panorama-'.$x.'-xml', $upload); update_post_meta($id, 'panorama-'.$x.'-xml', $upload);		
						}
					} else {
						wp_die("The file type that you've uploaded is not an XML file.");
					}	
				}
				for($y = 1; $y <= 18; $y++)
				{
					// Make sure the image file array isn't empty
					if(!empty($_FILES['panorama-'.$x.'-image-'.$y]['name'])) {
						// Check if the type is supported. If not, throw an error.
						$upload = $_FILES['panorama-'.$x.'-image-'.$y];
						$type = wp_check_filetype($upload['name'], array('png' => 'image/png', 'jpg' => 'image/jpeg'));		
					
						if($type['type']) {
							// Use the WordPress API to upload the file
							$upload = wp_handle_upload($upload, array( 'test_form' => false ));
							if(isset($upload['error']) && $upload['error'] != 0) {
								wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
							} else {
								add_post_meta($id, 'panorama-'.$x.'-image-'.$y, $upload); update_post_meta($id, 'panorama-'.$x.'-image-'.$y, $upload);		
							}
						} else {
							wp_die("The file type that you've uploaded is not an image file. (Error: Image ".$y." )");
						}	
					}
				}
			}
		}
		else{
			$num_panoramas = '0'; add_post_meta($id, 'num_panoramas', $num_panoramas); update_post_meta($id, 'num_panoramas', $num_panoramas);
		}
	}
	add_action('save_post', 'panoramas_save_data');
	
	class panoramas extends WP_Widget 
	{
		// constructor
    	function panoramas() 
		{
        	parent::WP_Widget(false, $name = __('Panoramas', 'wp_widget_plugin') );
    	}

		// display widget
		function widget($args, $instance) 
		{
			//Enque Styles
			wp_enqueue_style('housing_bootstrapCSS');
			wp_enqueue_style('housing_moduleCSS');
			wp_enqueue_style('housing_modalcustomCSS');
		
			//Enque Scripts
			wp_enqueue_script('jquery');
			wp_enqueue_script('housing_bootstrapJS');
			wp_enqueue_script('housing_panorama_gyroJS');
			wp_enqueue_script('housing_panorama_panoJS');
			wp_enqueue_script('housing_panorama_swfobjectJS');
			wp_enqueue_script('housing_panorama_switchJS');
			wp_enqueue_script('housing_modalcustomJS'); 
			
			$post_id = $GLOBALS['post']->ID;
			$num_panoramas = get_post_meta( $post_id, 'num_panoramas', true );
		
			if(!$num_panoramas || $num_panoramas == 'null')
			{
				return;
			}
			else
			{ ?>
				<div class="media-module" style="margin-bottom: 19px">
					<div class="header"><h3>Panorama</h3></div>
						<div class="body" id="pano">
							<ul class="thumbnails">
								<?php 
									$page_title = get_the_title();
									for($z = 1; $z <= $num_panoramas; $z++)
									{
										$title = get_post_meta( $post_id, 'panorama-'.$z.'-title', true );
										$caption = get_post_meta( $post_id, 'panorama-'.$z.'-description', true );
										$preview_image = get_post_meta( $post_id, 'panorama-'.$z.'-preview', true );
										$swf_file = get_post_meta( $post_id, 'panorama-'.$z.'-swf', true );
										$xml_file = get_post_meta( $post_id, 'panorama-'.$z.'-xml', true );
										?>
										<li class="thumbnail-container" data-panonum="<?php echo $z; ?>" style="display: <?php if($z!=1){echo 'none';}else{echo 'inherit';} ?>;"> 
											<a class="thumbnail modal-custom" data-panorama="panoramas" data-pagetitle="<?php echo esc_attr($page_title); ?>" data-xml="<?php echo esc_attr($xml_file['url']); ?>" title="<?php echo esc_attr($caption); ?>" href="<?php echo esc_attr($swf_file['url']); ?>"><img alt="<?php echo esc_attr($title); ?>" src="<?php echo esc_attr($preview_image['url']); ?>" width="100%"/></a> 
											<div style="margin-bottom: 9px"><?php if($num_panoramas > 1){ ?><a class="arrow" href="#" style="float: left"><span class="arrow-left"></span></a><a class="arrow" href="#" style="float: right"><span><span class="arrow-right"></span></a><?php } ?><div class="title"><span><?php echo esc_attr($caption); ?></span></div></div> 
										</li> 
										<?php 
									}
								?>
							</ul> 
						</div> 
					</div>
				<?php
			}
		}
	}	
	// register widget
	add_action('widgets_init', create_function('', 'return register_widget("panoramas");'));
?>