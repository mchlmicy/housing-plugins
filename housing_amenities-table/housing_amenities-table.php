<?php
	/*
	Plugin Name: Housing - Amenities Table
	Plugin URI: http://housing.pages.tcnj.edu/
	Description: A plugin that displays a table of the amenities in a residence hall.
	Version: 1.2.2
	Author: Patrick Kelly & Michael Young
	License: See readme.txt
	*/

	//Adds a box to the main column on the Post and Page edit screens.
	function amenities_edit_box() 
	{
		add_meta_box(
            'amenities_textdomain',
            __( 'Building Amenities', 'amenities_textdomain' ),
            'amenities_inner_edit_box',
            'page'
        );
	}
	if(is_active_widget(false, false, 'amenities_table', true)) 
	{
		add_action( 'add_meta_boxes', 'amenities_edit_box' );
		
		//Register Styles
		wp_register_style('amenitiesTableCSS', plugins_url('/css/amenities_table.css', __FILE__), false, '1.0.0', 'all');
			
		//Enqueue Styles
		wp_enqueue_style('amenitiesTableCSS');
		
		//Register Scripts
		wp_register_script('amenitiesTable_metaboxJS', plugins_url('/js/metabox-amenities_table.js', __FILE__), false, '1.0.0', 'all');
			
		//Queue Scripts
		wp_enqueue_script('jquery');
		wp_enqueue_script('amenitiesTable_metaboxJS');
	}

	//Prints the box content.
	function amenities_inner_edit_box($post) 
	{
		// Add an nonce field so we can check for it later.
  		wp_nonce_field(plugin_basename(__FILE__), 'amenities_inner_edit_box_nonce' );
	
		global $post;
		$post_id = $post->ID;
 	
		//Building Features
		$total_beds = get_post_meta( $post_id, 'total_beds', true );
    	$num_singles = get_post_meta( $post_id, 'num_singles', true );
		$num_doubles = get_post_meta( $post_id, 'num_doubles', true );
		$num_triples = get_post_meta( $post_id, 'num_triples', true );
		$num_quads = get_post_meta( $post_id, 'num_quads', true );
		$wifi = get_post_meta( $post_id, 'wifi', true );
		$wifi_scheduled = get_post_meta( $post_id, 'wifi_scheduled', true );
		$air_conditioning = get_post_meta( $post_id, 'air_conditioning', true );
		$bathrooms = get_post_meta($post_id, 'bathrooms', true);
	
		//Room Amenities
    	$overhead_lighting = get_post_meta( $post_id, 'overhead_lighting', true );
    	$cable = get_post_meta( $post_id, 'cable', true );
		$carpet = get_post_meta( $post_id, 'carpet', true );
		$num_ethernet_jacks = get_post_meta( $post_id, 'num_ethernet_jacks', true );
	
		//Provided For Each Resident
		$chair = get_post_meta( $post_id, 'chair', true );
		$closet_or_wardrobe = get_post_meta( $post_id, 'closet_or_wardrobe', true );
		$desk = get_post_meta( $post_id, 'desk', true );
		$bed = get_post_meta( $post_id, 'bed', true );
	
		//Table Caption
		$table_caption = get_post_meta( $post_id, 'table_caption', true );

		?>
        <div class="amenity-tab-container">
        	<a class="amenity-tab selected" href="#" data-tab="building_features"><div><mobile>Features</mobile><desktop>Building Features</desktop></div></a>
			<a class="amenity-tab" href="#" data-tab="room_amenities"><div><mobile>Amenities</mobile><desktop>Room Amenities</desktop></div></a>
            <a class="amenity-tab" href="#" data-tab="provided_students"><div><mobile>Provided</mobile><desktop>Provided for Each Student</desktop></div></a>
            <a class="amenity-tab" href="#" data-tab="table_caption"><div><mobile>Caption</mobile><desktop>Table Caption</desktop></div></a>
        </div>
        <div class="amenity-container">
            <div id="building_features">
                <div>
                    <strong class="amenity-title">Total Beds</strong>
                    <input type="text" id='total_beds' name='total_beds' value="<?php echo $total_beds; ?>">
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title"># of Singles</strong>
                    <input type="text" id='num_singles' name='num_singles' value="<?php echo $num_singles; ?>">
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title"># of Doubles</strong>
                    <input type="text" id='num_doubles' name='num_doubles' value="<?php echo $num_doubles; ?>">
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title"># of Triples</strong>
                    <input type="text" id='num_triples' name='num_triples' value="<?php echo $num_triples; ?>">
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title"># of Quads</strong>
                    <input type="text" id='num_quads' name='num_quads' value="<?php echo $num_quads; ?>">
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title">Wifi</strong>
                    <span><input type="radio" id="wifi" name="wifi" value="Yes" onclick="hideWifi();" <?php if($wifi=='Yes'){echo 'checked';} ?>> Yes</span>
                    <span><input type="radio" id="wifi" name="wifi" value="No" onclick="hideWifi();" <?php if($wifi=='No'){echo 'checked';} ?>> No</span>
                    <span><input type="radio" id="wifi" name="wifi" value="scheduled" onclick="showWifi();" <?php if($wifi=='scheduled'){echo 'checked';} ?>> Scheduled</span>
                </div>
                <div id="wifi-hidden" style="<?php if($wifi!='scheduled'){echo 'display:none';}; ?>">
                    <strong class="amenity-title">&nbsp;</strong>
                    <input type="text" style="margin-top: 9px" id='wifi_scheduled' placeholder='Scheduled for...' name='wifi_scheduled' value="<?php echo $wifi_scheduled; ?>">
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title">Air Conditioning</strong>
                    <span class="amenity-item_inline"><input type="radio" id="air_conditioning" name="air_conditioning" value="Yes" <?php if($air_conditioning=='Yes'){echo 'checked';} ?>> Yes</span>
                    <span class="amenity-item_inline"><input type="radio" id="air_conditioning" name="air_conditioning" value="No" <?php if($air_conditioning=='No'){echo 'checked';} ?>> No</span>
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title">Bathrooms</strong>
                    <select name='bathrooms' id='bathrooms' <?php echo $bathrooms ?>>
              			<option></option>
            			<option value="Personal" <?php if($bathrooms=='Personal'){echo 'selected="selected"'; }; ?>>Personal</option>
              			<option value="Communal" <?php if($bathrooms=='Communal'){echo 'selected="selected"'; }; ?>>Communal</option>
              			<option value="Suite-Style" <?php if($bathrooms=='Suite-Style'){echo 'selected="selected"'; }; ?>>Suite-Style</option>
                    </select>
                </div>
            </div>
            <div id="room_amenities" style="display: none">
                <div>
                    <strong class="amenity-title">Overhead Lighting</strong>
                    <span class="amenity-item_inline"><input type="radio" id="overhead_lighting" name="overhead_lighting" value="Yes" <?php if($overhead_lighting=='Yes'){echo 'checked';} ?>> Yes</span>
                    <span class="amenity-item_inline"><input type="radio" id="overhead_lighting" name="overhead_lighting" value="No" <?php if($overhead_lighting=='No'){echo 'checked';} ?>> No</span>
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title">Cable</strong>
                    <span class="amenity-item_inline"><input type="radio" id="cable" name="cable" value="Yes" <?php if($cable=='Yes'){echo 'checked';} ?>> Yes</span>
                    <span class="amenity-item_inline"><input type="radio" id="cable" name="cable" value="No" <?php if($cable=='No'){echo 'checked';} ?>> No</span>
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title">Carpet</strong>
                    <span class="amenity-item_inline"><input type="radio" id="carpet" name="carpet" value="Yes" <?php if($carpet=='Yes'){echo 'checked';} ?>> Yes</span>
                    <span class="amenity-item_inline"><input type="radio" id="carpet" name="carpet" value="No" <?php if($carpet=='No'){echo 'checked';} ?>> No</span>
                    <span class="amenity-item_inline"><input type="radio" id="carpet" name="carpet" value="Only basement rooms" <?php if($carpet=='Only basement rooms'){echo 'checked';} ?>> Only basement rooms</span>
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title"># of Ethernet Jacks</strong>
                    <input type="text" id='num_ethernet_jacks' name='num_ethernet_jacks' value="<?php echo $num_ethernet_jacks; ?>">
                </div>
            </div>
            <div id="provided_students" style="display: none">
                <div>
                    <strong class="amenity-title">Chair</strong>
                    <span class="amenity-item_inline"><input type="radio" id="chair" name="chair" value="Yes" <?php if($chair=='Yes'){echo 'checked';} ?>> Yes</span>
                    <span class="amenity-item_inline"><input type="radio" id="chair" name="chair" value="No" <?php if($chair=='No'){echo 'checked';} ?>> No</span>
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title">Closet or Wardrobe</strong>
                    <span class="amenity-item_inline"><input type="radio" id="closet_or_wardrobe" name="closet_or_wardrobe" value="Yes" <?php if($closet_or_wardrobe=='Yes'){echo 'checked';} ?>> Yes</span>
                    <span class="amenity-item_inline"><input type="radio" id="closet_or_wardrobe" name="closet_or_wardrobe" value="No" <?php if($closet_or_wardrobe=='No'){echo 'checked';} ?>> No</span>
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title">Desk</strong>
                    <span class="amenity-item_inline"><input type="radio" id="desk" name="desk" value="Yes" <?php if($desk=='Yes'){echo 'checked';} ?>> Yes</span>
                    <span class="amenity-item_inline"><input type="radio" id="desk" name="desk" value="No" <?php if($desk=='No'){echo 'checked';} ?>> No</span>
                </div>
                <hr/>
                <div>
                    <strong class="amenity-title">Bed</strong>
                    <span class="amenity-item_inline"><input type="radio" id="bed" name="bed" value="Yes" <?php if($bed=='Yes'){echo 'checked';} ?>> Yes</span>
                    <span class="amenity-item_inline"><input type="radio" id="bed" name="bed" value="No" <?php if($bed=='No'){echo 'checked';} ?>> No</span>
                </div>
            </div>
            <div id="table_caption" style="display: none">
                <textarea name='table_caption' id='table_caption' style="width: 100%; resize: vertical" rows="3"><?php echo $table_caption; ?></textarea></td>
            </div>
      	</div>
  		<?php
	}
	function amenities_save_data($id) 
	{
		//Security verification
		if(!wp_verify_nonce($_POST['amenities_inner_edit_box_nonce'], plugin_basename(__FILE__))){return $id;} 
	  
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
		{
	  		return $id;
		} 
	  
		if('page' == $_POST['post_type']) 
		{
	  		if(!current_user_can('edit_page', $id)) 
			{
	    		return $id;
	  		} 
		} 
		else 
		{
   			if(!current_user_can('edit_page', $id)) 
			{
	    		return $id;
	   		} 
		}	 
	
		//Building Features
		if($_POST['total_beds']!=null){			$data = $_POST['total_beds'];		update_post_meta($id, 'total_beds', $data);}
		if($_POST['num_singles']!=null){		$data = $_POST['num_singles'];		update_post_meta($id, 'num_singles', $data);}
		if($_POST['num_doubles']!=null){		$data = $_POST['num_doubles'];		update_post_meta($id, 'num_doubles', $data);}
		if($_POST['num_triples']!=null){		$data = $_POST['num_triples'];		update_post_meta($id, 'num_triples', $data);}
		if($_POST['num_quads']!=null){			$data = $_POST['num_quads'];		update_post_meta($id, 'num_quads', $data);}
		if($_POST['wifi']!=null){				$data = $_POST['wifi'];				update_post_meta($id, 'wifi', $data);}
		if($_POST['wifi_scheduled']!=null){		$data = $_POST['wifi_scheduled'];	update_post_meta($id, 'wifi_scheduled', $data);}
		if($_POST['air_conditioning']!=null){	$data = $_POST['air_conditioning'];	update_post_meta($id, 'air_conditioning', $data);}
		if($_POST['bathrooms']!=null){ $data = $_POST['bathrooms'];		update_post_meta($id, 'bathrooms', $data);}
	
		//Room Amenities
		if($_POST['overhead_lighting']!=null){	$data = $_POST['overhead_lighting'];	update_post_meta($id, 'overhead_lighting', $data);}
		if($_POST['cable']!=null){				$data = $_POST['cable'];				update_post_meta($id, 'cable', $data);}
		if($_POST['carpet']!=null){				$data = $_POST['carpet'];				update_post_meta($id, 'carpet', $data);}
		if($_POST['num_ethernet_jacks']!=null){	$data = $_POST['num_ethernet_jacks'];	update_post_meta($id, 'num_ethernet_jacks', $data);}
	
		//Provided For Each Resident
		if($_POST['chair']!=null){				$data = $_POST['chair'];				update_post_meta($id, 'chair', $data);}
		if($_POST['closet_or_wardrobe']!=null){	$data = $_POST['closet_or_wardrobe'];	update_post_meta($id, 'closet_or_wardrobe', $data);}
		if($_POST['desk']!=null){				$data = $_POST['desk'];					update_post_meta($id, 'desk', $data);}
		if($_POST['bed']!=null){				$data = $_POST['bed'];					update_post_meta($id, 'bed', $data);}

		//Table Caption
		if($_POST['table_caption']!=null){$data = $_POST['table_caption'];	update_post_meta($id, 'table_caption', $data);}
	}
	add_action('save_post', 'amenities_save_data');

	class amenities_table extends WP_Widget 
	{
		//Constructor
    	function amenities_table() 
		{
        	parent::WP_Widget(false, $name = __('Amenities Table', 'amenities_table') );
    	}

		//Display widget
		function widget($args, $instance) 
		{
			//Register styles
			wp_register_style('amenitiesTableCSS', plugins_url('/css/amenities_table.css', __FILE__), false, '1.0.0', 'all');
			
			//Enqueue Styles
			wp_enqueue_style('amenitiesTable_tableCSS');
	
			//Variables
			global $post;
			
			$amenities_building_beds = get_post_meta($post->ID, 'total_beds', true);
			$amenities_building_singles = get_post_meta($post->ID, 'num_singles', true);
			$amenities_building_doubles = get_post_meta($post->ID, 'num_doubles', true);
			$amenities_building_triples = get_post_meta($post->ID, 'num_triples', true);
			$amenities_building_quads = get_post_meta($post->ID, 'num_quads', true);
			$amenities_building_wifi = get_post_meta($post->ID, 'wifi', true);
			$amenities_building_wifischeduled = get_post_meta($post->ID, 'wifi_scheduled', true);
			$amenities_building_airconditioning = get_post_meta($post->ID, 'air_conditioning', true);
			$amenities_building_bathrooms = get_post_meta($post->ID, 'bathrooms', true);
			
			$amenities_room_lighting = get_post_meta($post->ID, 'overhead_lighting', true);
			$amenities_room_cable = get_post_meta($post->ID, 'cable', true);
			$amenities_room_carpet = get_post_meta($post->ID, 'carpet', true);
			$amenities_room_ethernet = get_post_meta($post->ID, 'num_ethernet_jacks', true);
			
			$amenities_provided_chair = get_post_meta($post->ID, 'chair', true);
			$amenities_provided_closet = get_post_meta($post->ID, 'closet_or_wardrobe', true);
			$amenities_provided_desks = get_post_meta($post->ID, 'desk', true);
			$amenities_provided_beds = get_post_meta($post->ID, 'bed', true);
			
			$amenities_table_caption = get_post_meta($post->ID, 'table_caption', true);
							
			//If any of the building features are not null...
			if($amenities_building_beds!=null || $amenities_building_singles!=null || $amenities_building_doubles!=null || $amenities_building_triples!=null || $amenities_building_quads!=null || $amenities_building_wifi!=null || $amenities_building_airconditioning!=null || $amenities_building_bathrooms!=null)
			{
				$amenities_building = true;
			}
								
			//If any of the room amenities are not null...
			if($amenities_room_lighting!=null || $amenities_room_cable!=null || $amenities_room_carpet!=null || $amenities_room_ethernet!=null)
			{
				$amenities_room = true;
			}
								
			//If any of the provided to each student are not null...
			if($amenities_provided_chair!=null || $amenities_provided_closet!=null || $amenities_provided_desks!=null || $amenities_provided_beds!=null)
			{
				$amenities_provided = true;
			}
								
			//If any of the amenities sections are being displayed...
			if($amenities_building==true || $amenities_room==true || $amenities_provided==true || $amenities_table_caption!=null)
			{
				echo "	<table class='amenitiestable'>";
									
				if($amenities_building==true)
				{
					echo "	<thead><th colspan='2' style='text-transform: inherit'><h3>Amenities</h3></th></thead>
							<tr><td colspan='2' class='subheader'><center><h5>Building Features</h5></center></td></tr>
						";
										
					if($amenities_building_beds!=null)
					{
						echo "	<tr><td>Total Beds</td><td>".esc_attr($amenities_building_beds)."</td></tr>";
					}
					if($amenities_building_singles!=null)
					{
						echo "	<tr><td># of Singles</td><td>".esc_attr($amenities_building_singles)."</td></tr>";
					}
					if($amenities_building_doubles!=null)
					{
						echo "	<tr><td># of Doubles</td><td>".esc_attr($amenities_building_doubles)."</td></tr>";
					}
					if($amenities_building_triples!=null)
					{
						echo "	<tr><td># of Triples</td><td>".esc_attr($amenities_building_triples)."</td></tr>";
					}
					if($amenities_building_quads!=null)
					{
						echo "	<tr><td># of Quads</td><td>".esc_attr($amenities_building_quads)."</td></tr>";
					}
					if($amenities_building_wifi!=null)
					{
						if($amenities_building_wifi=='scheduled')
						{
							echo "<tr><td>Wifi</td><td>".esc_attr($amenities_building_wifischeduled)."</td></tr>";
						}
						else
						{
							echo "<tr><td>Wifi</td><td>".esc_attr($amenities_building_wifi)."</td></tr>";
						}
					}
					if($amenities_building_airconditioning!=null)
					{
						echo "	<tr><td>Air Conditioning</td><td>".esc_attr($amenities_building_airconditioning)."</td></tr>";
					}
					if($amenities_building_bathrooms!=null)
					{
						echo "	<tr><td>Bathrooms</td><td>".esc_attr($amenities_building_bathrooms)."</td></tr>";
					}						
				}
									
				if($amenities_room==true)
				{
					echo "		<tr><td colspan='2' class='subheader'><center><h5>Room Amenities</h5></center></td></tr>
						";
									
					if($amenities_room_lighting!=null)
					{
						echo "	<tr><td>Overhead Lighting</td><td>".esc_attr($amenities_room_lighting)."</td></tr>";
					}
					if($amenities_room_cable!=null)
					{
						echo "	<tr><td>Cable</td><td>".esc_attr($amenities_room_cable)."</td></tr>";
					}
					if($amenities_room_carpet!=null)
					{
						echo "	<tr><td>Carpet</td><td>".esc_attr($amenities_room_carpet)."</td></tr>";
					}
					if($amenities_room_ethernet!=null)
					{
						echo "	<tr><td># of Ethernet Jacks</td><td>".esc_attr($amenities_room_ethernet)."</td></tr>";
					}
				}
									
				if($amenities_provided==true)
				{
					echo "		<tr><td colspan='2' class='subheader'><center><h5>Provided for Each Resident</h5></center></td></tr>
						";
									
					if($amenities_provided_chair!=null)
					{
						echo "	<tr><td>Chair</td><td>".esc_attr($amenities_provided_chair)."</td></tr>";
					}
					if($amenities_provided_closet!=null)
					{
						echo "	<tr><td>Closet or Wardrobe</td><td>".esc_attr($amenities_provided_closet)."</td></tr>";
					}
					if($amenities_provided_desks!=null)
					{
						echo "	<tr><td>Desk</td><td>".esc_attr($amenities_provided_desks)."</td></tr>";
					}
					if($amenities_provided_beds!=null)
					{
						echo "	<tr><td>Bed</td><td>".esc_attr($amenities_provided_beds)."</td></tr>";
					}
				}
									
				if($amenities_table_caption!=null)
				{
						echo "	<tr style='border-top: 1px solid #e3e3e3'><td colspan='2' class='footer'>".esc_attr($amenities_table_caption)."</td></tr>
							";
				}
				
				echo "	</table>";
			}	
		}
	}	
	// register widget
	add_action('widgets_init', create_function('', 'return register_widget("amenities_table");'));
?>