<?php
	/*
	Plugin Name: Housing - Modules
	Plugin URI: http://housing.pages.tcnj.edu/
	Description: A plugin that displays modules of text on pages.
	Version: 1.1.4
	Author: Patrick Kelly & Michael Young
	License: See readme.txt
	*/

	//Adds input to all pages
 	function modules_edit_box() 
	{
		add_meta_box(
            //ID
			'modules_textdomain',
            //Title
			__( 'Modules', 'modules_textdomain' ),
           	//Function to call
		   	'modules_inner_edit_box',
            //Add to
			'page'
        );
	}
	//Add input to pages with widget
	if(is_active_widget(false, false, 'modules', true) && is_plugin_page()!='True') 
	{
		add_action('add_meta_boxes', 'modules_edit_box');
	}
	
	//Register Style
	wp_register_style('housingModules_editboxCSS', plugins_url('/css/editbox.css', __FILE__), false, '1.0.0', 'all');
	
	//Register Scripts
	wp_register_script('housingModules_editboxJS', plugins_url('/js/editbox.js', __FILE__), false, '1.0.0', 'all');
			
	//Queue Scripts
	wp_enqueue_script('housingModules_editboxJS');
	
	//Queue Styles
	wp_enqueue_style('housingModules_editboxCSS');

	//Prints the box content
	function modules_inner_edit_box($post) 
	{
		//Add an nonce field so we can check for it later.
  		wp_nonce_field(plugin_basename(__FILE__), 'modules_inner_edit_box_nonce' );

  		?>
       	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<div>
        	<div style="display: inline-block; margin: 4.5px 0px; width: 25%"><strong>Modules</strong></div>
            <select style="margin: 0px 4.5px 4.5px 12px" name="num_modules" onchange="selectModules(jQuery(this).val())">
				<option>Number of modules</option>
				<?php
					$x = 1;
					while($x <= 4)
					{
						if($x != get_post_meta($post->ID, 'num_modules', true)){echo '<option value="'.$x.'">'.$x.'</option>';}
						else{echo '<option value="'.$x.'" selected>'.$x.'</option>';} 
						$x++;
					}
				?>
          	</select>
        </div>
        <div id="modules-tab-container"></div>
        <div class="modules-container">
		<?php
			$x = 1;
			while($x <= 4)
			{
				?>
                <div id="module-<?php echo $x; ?>" style="display: none;">
                    <div style="margin-bottom: 12px">
                        <strong style="font-size: 15px">Module <?php echo $x; ?></strong>
                    </div>
                    <div style="margin-bottom: 12px">
                        <div style="margin-bottom: 6px">
                            <div style="display: inline-block; width: 25%"><strong>Title</strong></div>
                            <input type="text" style="margin: 0px 4.5px 4.5px 12px" name="module-<?php echo $x; ?>-title" value="<?php echo get_post_meta($post->ID, 'module-'.$x.'-title', true); ?>">
                    	</div>
                        <div style="margin-bottom: 6px">
                            <div style="display: inline-block; vertical-align: top; width: 25%"><strong>Body</strong></div>
                            <textarea style="margin: 0px 4.5px 4.5px 12px" name="module-<?php echo $x; ?>-body"><?php echo get_post_meta($post->ID, 'module-'.$x.'-body', true); ?></textarea>
                    	</div>
                        <div style="margin-bottom: 6px">
                            <div style="display: inline-block; width: 25%"><strong>Link</strong></div>
                            <input type="text" style="margin: 0px 4.5px 4.5px 12px" name="module-<?php echo $x; ?>-link" value="<?php echo get_post_meta($post->ID, 'module-'.$x.'-link', true); ?>" placeholder="Field not required.">
                    	</div>
                        <div style="margin-bottom: 6px">
                            <div style="display: inline-block; width: 25%"><strong>Link Text</strong></div>
                            <input type="text" style="margin: 0px 4.5px 4.5px 12px" name="module-<?php echo $x; ?>-linktext" value="<?php echo get_post_meta($post->ID, 'module-'.$x.'-linktext', true); ?>" placeholder="Field not required.">
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

	//Saves box content
	function modules_save_data($id) 
	{
		//Verify nonce is set
		if(!wp_verify_nonce($_POST['modules_inner_edit_box_nonce'], plugin_basename(__FILE__))) 
		{
	  		return $id;
		}
	  
		//If autosave...
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
		{
		  	return $id;
		} 
	  
		//Check user permissions of user submitting
		if('page'==$_POST['post_type']) 
		{
	  		if(!current_user_can('edit_page', $id)) 
			{
	    		return $id;
	  		} 
	 		else if(!current_user_can('edit_page', $id)) 
			{
	    		return $id;
	   		} 
		}
	
		// ----- Save -----
		// Modules
		$modules = intval($_POST['num_modules']); add_post_meta($id, 'num_modules', $modules); update_post_meta($id, 'num_modules', $modules);
		if(is_int($modules))
		{
			for($x = 1; $x <= $modules; $x++)
			{
				// Module Inputs
				$data = $_POST['module-'.$x.'-title']; add_post_meta($id, 'module-'.$x.'-title', $data); update_post_meta($id, 'module-'.$x.'-title', $data);
				$data = $_POST['module-'.$x.'-body']; add_post_meta($id, 'module-'.$x.'-body', $data); update_post_meta($id, 'module-'.$x.'-body', $data);
				$data = $_POST['module-'.$x.'-link']; add_post_meta($id, 'module-'.$x.'-link', $data); update_post_meta($id, 'module-'.$x.'-link', $data);
				$data = $_POST['module-'.$x.'-linktext']; add_post_meta($id, 'module-'.$x.'-linktext', $data); update_post_meta($id, 'module-'.$x.'-linktext', $data);
			}
		}
	}
	add_action('save_post', 'modules_save_data');

	//Modules Widget Class
	class modules extends WP_Widget 
	{
		//Constructor
    	function modules() 
		{
        	parent::WP_Widget(false, $name = __('Modules', 'wp_widget_plugin') );
    	}

		//Display widget
		function widget($args, $instance) 
		{
			//Styles
			wp_register_style('modules_moduleCSS', plugins_url('/css/module.css', __FILE__), false, '1.0.0', 'all');
			
			//Queue Styles
			wp_enqueue_style('modules_moduleCSS');
			
			//Variables
			$post_id = $GLOBALS['post']->ID;
			$modules = intval(get_post_meta($post_id, 'num_modules', true));
			
			if(is_int($modules))
			{
				for($x = 1; $x <= $modules; $x++)
				{
					$title = get_post_meta($post_id, 'module-'.$x.'-title', true);
					$body = get_post_meta($post_id, 'module-'.$x.'-body', true);
					
					if($title!=null && $body!=null)
					{
						echo '	<div class="module">
									<div class="header"><h3>'.$title.'</h3></div>
									<div class="body">
										<p>'.$body.'</p>
							';
					}
					
					$link = get_post_meta($post_id, 'module-'.$x.'-link', true);
					$linktext = get_post_meta($post_id, 'module-'.$x.'-linktext', true);
						
					if($link!=null && $linktext!=null)
					{
						echo '			<a style="border-top: 1px solid #e3e3e3; display: block; padding: 4px 0px; text-align: center" href="'.$link.'" class="module-link">'.$linktext.'</a>';
					}
					
					echo '			</div>	
								</div>';
				}
			}
		}
	}	
	add_action('widgets_init', create_function('', 'return register_widget("modules");'));
?>