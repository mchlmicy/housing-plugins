<?php
	/*
	Plugin Name: Housing - News Feed
	Plugin URI: http://housing.pages.tcnj.edu/
	Description: A plugin that displays news items from an RSS feed.
	Version: 1.0.1
	Author: Patrick Kelly & Michael Young
	License: See readme.txt
	*/

	//Adds input to all pages
 	function newsfeed_edit_box() 
	{
		add_meta_box(
            //ID
			'newsfeed_textdomain',
            //Title
			__( 'Newsfeed', 'newsfeed_textdomain' ), 
           	//Function to call
		   	'newsfeed_inner_edit_box',
            //Add to
			'page'
        );
	}
	//Add input to pages with widget
	if(is_active_widget(false, false, 'newsfeed', true) && is_plugin_page()!='True') 
	{
		add_action('add_meta_boxes', 'newsfeed_edit_box');
	}


	//Prints the box content
	function newsfeed_inner_edit_box($post) 
	{
		//Add an nonce field so we can check for it later.
  		wp_nonce_field(plugin_basename(__FILE__), 'newsfeed_inner_edit_box_nonce' );

  		//Retrieve existing values from the database to use in the form
	  	$newsfeed_rss_value = get_post_meta( $post->ID, 'newsfeed_rss', true);
		
		echo '	<h4>RSS Feed</h4>
				<input type="text" id="newsfeed_rss" name="newsfeed_rss" style="width: 100%" value="'.esc_attr($newsfeed_rss_value).'"/>
			';
	}
	
	//Saves box content
	function newsfeed_save_data($id) 
	{	
		//Verify nonce is set
		if(!wp_verify_nonce($_POST['newsfeed_inner_edit_box_nonce'], plugin_basename(__FILE__))) 
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
	
		//Check if inputs are empty and return data
		$data = $_POST['newsfeed_rss']; 			
		add_post_meta($id, 'newsfeed_rss', $data); 		
		update_post_meta($id, 'newsfeed_rss', $data);
	}
	add_action('save_post', 'newsfeed_save_data');

	//Newsfeed Widget Class
	class newsfeed extends WP_Widget 
	{
		//Constructor
    	function newsfeed() 
		{
        	parent::WP_Widget(false, $name = __('Newsfeed', 'wp_widget_plugin') );
    	}

		//Display widget
		function widget($args, $instance) 
		{
			//Styles
			wp_register_style('newsfeed_newsfeedCSS', plugins_url('/css/newsfeed.css', __FILE__), false, '1.0.0', 'all');
			
			//Queue Styles
			wp_enqueue_style('newsfeed_newsfeedCSS');
			
			//Variables
			$post_id = $GLOBALS['post']->ID;
			$newsfeed_rss_value = get_post_meta( $post_id, 'newsfeed_rss', true);
			
			//Create a new DOM Document
			$doc = new DOMDocument();
									
			//If the document can be loaded...
			if($doc->load($newsfeed_rss_value))
			{
				echo '	<h1>TCNJ News</h1>
						<div class="well rss">
				';
				
				foreach($doc->getElementsbyTagName('item') as $newsitem)
				{
					$newsitem_title = $newsitem->getElementsByTagName('title')->item(0)->nodeValue;
					$newsitem_link = $newsitem->getElementsByTagName('link')->item(0)->nodeValue;
					$newsitem_timestamp = $newsitem->getElementsByTagName('pubDate')->item(0)->nodeValue;
					$newsitem_content = $newsitem->getElementsByTagName('encoded')->item(0)->nodeValue;
											
					//Format timestamp
					date_default_timezone_set('America/New_York');			 
					$newsitem_timestampformatted = date('F d \a\t g:ia', strtotime($newsitem_timestamp));                        
												
					echo	"	<div class='item'>	                        
									<h3 title='{$newsitem_title}'><a href='$newsitem_link}'>{$newsitem_title}</a></h3>
									<div>{$newsitem_content}</div>
									<div class='timestamp'>
										<strong>{$newsitem_timestampformatted}</strong>
									</div>
									<hr style='clear: both'/>
								</div>
							";										
				}
				
				echo '	</div>
					';
			}
			else
			{
				//Return Errors
			}
		}
	}	
	add_action('widgets_init', create_function('', 'return register_widget("newsfeed");'));
?>