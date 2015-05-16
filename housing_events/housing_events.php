<?php
	/*
		Plugin Name: Housing - Events
		Plugin URI: http://housing.pages.tcnj.edu/
		Description: A plugin that manages and displays events.
		Version: 1.0.0
		Author: Patrick Kelly & Michael Young
		License: See readme.txt
	*/
	
	// Define Event post type
	function housing_events_init() 
	{
	  $labels = array(
		'name'               => 'Events',
		'singular_name'      => 'Event',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Event',
		'edit_item'          => 'Edit Event',
		'new_item'           => 'New Event',
		'all_items'          => 'All Events',
		'view_item'          => 'View Event',
		'search_items'       => 'Search Events',
		'not_found'          => 'No events found',
		'not_found_in_trash' => 'No events found in Trash',
		'parent_item_colon'  => '',
		'menu_name'          => 'Events'
	  );
	
	  $args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array('slug' => 'event'),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'supports'           => array('title', 'editor')
	  );
	
	  register_post_type('event', $args);
	  flush_rewrite_rules();
	}
	add_action('init', 'housing_events_init');
	
	// Adds a box to the main column on the Event edit screens.
	function housing_events_edit_box() 
	{
		add_meta_box(
				'housing_events_textdomain',
				__( 'Event Details', 'housing_events_textdomain' ),
				'housing_events_inner_edit_box',
				'event'
			);
	}
	add_action( 'add_meta_boxes', 'housing_events_edit_box' );
		
	/* Filter the single_template with our custom function*/
	function get_housing_event_single_template($single_template) {
    	global $post;
	    if ($post->post_type == 'event') {
			$single_template = dirname( __FILE__ ) . '/single-event.php';
			}
		return $single_template;
	}
	add_filter( 'single_template', 'get_housing_event_single_template' );
	
	// define custom archive template
	function get_housing_event_archive_template( $archive_template ) {			 
    	if ( is_post_type_archive ( 'event' ) ) {
    		return dirname(__FILE__).'/archive-event.php';
		}
	}
	add_filter( 'archive_template', 'get_housing_event_archive_template' );

	//flush rewrite rules if our rewrite rule isn't yet included
	add_action('admin_init', 'handle_event_route');
	function handle_event_route()
	{
	    flush_rewrite_rules();
	}

	// add 'event' url
	add_filter('rewrite_rules_array', 'add_ics_event_rules');
	function add_ics_event_rules($rules) {
		$new_rules = array('event/([^/]+)/(ical)/?$' => 'index.php?event_ics=$matches[1]');
		$rules = $new_rules + $rules;
		return $rules;
	}
	
	add_filter('rewrite_rules_array', 'add_google_event_rules');
	function add_google_event_rules($rules){
		$new_rules = array('event/([^/]+)/(google)/?$' => 'index.php?event_google=$matches[1]');
		$rules = $new_rules + $rules;
		return $rules;
	}

	function declare_event_ics_query_vars( $vars )
	{
		$vars[] = 'event_ics';
		return $vars;
	}
	add_filter( 'query_vars', 'declare_event_ics_query_vars' );
	
	function declare_event_google_query_vars($vars){
		$vars[] = 'event_google';
		return $vars;
	}
	add_filter('query_vars', 'declare_event_google_query_vars');
	
	function event_ics_template($template) {
    	global $wp_query, $wpdb;
    	if ($wp_query->get( 'event_ics' )) {
			$event_slug = $wp_query->get('event_ics');
			$event_id = url_to_postid( '/event/'.$event_slug.'/' );
			$template = include(dirname(__FILE__).'/ics.php');
		}
		return $template;
	}
	add_action( 'template_include', 'event_ics_template', 99 );
	
	function event_google_template($template){
		global $wp_query, $wpdb;
		if ($wp_query->get('event_google')){
			$event_slug = $wp_query->get('event_google');
			$event_id = url_to_postid('/event/'.$event_slug.'/');
			$event_title = rawurlencode(get_the_title($event_id));
			$event_description = rawurlencode(get_post_field('post_content', $event_id));
			$event_datetime = '';
			
			// Define the event datetime
			$event_date_type = get_post_meta($event_id, 'date_type', true);
			if($event_date_type=='date'){
				$date_hour = get_post_meta($event_id, 'date_hour', true);
				$date_minute = get_post_meta($event_id, 'date_minute', true);if(intval($date_minute)<=9){$date_minute = '0'.$date_minute;}
				$date_ampm = get_post_meta($event_id, 'date_ampm', true);
				$date_month = get_post_meta($event_id, 'date_month', true);
				$date_day = get_post_meta($event_id, 'date_day', true);
				$date_year = get_post_meta($event_id, 'date_year', true);
		
				if($date_hour && $date_minute && $date_ampm && $date_month && $date_day && $date_year)
				{
					if($date_ampm == 'pm' && $date_hour != 12){$date_hour = $date_hour+12;}
					$dt = new DateTime($date_year.'-'.$date_month.'-'.$date_day.' '.$date_hour.':'.$date_minute.':00');
					$event_datetime_start = $event_datetime_end = $dt->format('Ymd').'T'.$dt->format('His');
					$event_datetime = $event_datetime_start.'/'.$event_datetime_end;
				}
			}
			else if ($event_date_type=='datespan'){
				$datespan_starthour = get_post_meta($event_id, 'datespan_starthour', true);
				$datespan_startminute = get_post_meta($event_id, 'datespan_startminute', true);if(intval($datespan_startminute)<=9){$datespan_startminute = '0'.$datespan_startminute;}
				$datespan_startampm = get_post_meta($event_id, 'datespan_startampm', true);
				$datespan_startmonth = get_post_meta($event_id, 'datespan_startmonth', true);
				$datespan_startday = get_post_meta($event_id, 'datespan_startday', true);
				$datespan_startyear = get_post_meta($event_id, 'datespan_startyear', true);
				$datespan_endhour = get_post_meta($event_id, 'datespan_endhour', true);
				$datespan_endminute = get_post_meta($event_id, 'datespan_endminute', true);if(intval($datespan_endminute)<=9){$datespan_endminute = '0'.$datespan_endminute;}
				$datespan_endampm = get_post_meta($event_id, 'datespan_endampm', true);
				$datespan_endmonth = get_post_meta($event_id, 'datespan_endmonth', true);
				$datespan_endday = get_post_meta($event_id, 'datespan_endday', true);
				$datespan_endyear = get_post_meta($event_id, 'datespan_endyear', true);
		
				if($datespan_starthour && $datespan_startminute && $datespan_startampm && $datespan_startmonth && $datespan_startday && $datespan_startyear)
				{
					if($datespan_startampm == 'pm' && $datespan_starthour != 12){$datespan_starthour = $datespan_starthour+12;}
					if($datespan_endampm == 'pm' && $datespan_endhour != 12){$datespan_endhour = $datespan_endhour+12;}

					$dtstart = new DateTime($datespan_startyear.'-'.$datespan_startmonth.'-'.$datespan_startday.' '.$datespan_starthour.':'.$datespan_startminute.':00');
					$dtend = new DateTime($datespan_endyear.'-'.$datespan_endmonth.'-'.$datespan_endday.' '.$datespan_endhour.':'.$datespan_endminute.':00');
			
					$event_datetime_start = $dtstart->format('Ymd').'T'.$dtstart->format('His');
					$event_datetime_end = $dtend->format('Ymd').'T'.$dtend->format('His');
					$event_datetime = $event_datetime_start.'/'.$event_datetime_end;
				}
			}
			// Define the event location
			$event_building_type = get_post_meta($event_id, 'building_type', true);
			$event_building_name = get_post_meta($event_id, 'building_name', true);
			if($event_building_type && $event_building_name)
			{
				if($event_building_type=='one' || $event_building_type=='custom'){$event_location = $event_building_name;}
				else if($event_building_type=='multi'){$x = 0; while($x < count($event_building_name)){if($x>0){$event_location = $event_location.', ';} $event_location = $event_location.$event_building_name[$x]; $x++;}}
			}
			else {
				$event_location = 'The College of New Jersey';
			}
			$google_event_url = "http://www.google.com/calendar/event?action=TEMPLATE&text=".$event_title."&dates=".$event_datetime."&details=".$event_description."&location=".urlencode($event_location)."&trp=false&sprop=[permalink]&sprop=name:[permaname]";
			wp_redirect($google_event_url);
		}
	}
	add_filter('template_redirect', 'event_google_template');
	
	//Register Scripts
	wp_register_script('housingEvents_editboxOpensectionsJS', plugins_url('/js/editbox-opensections.js', __FILE__), false, '1.0.0', 'all');
			
	//Queue Scripts
	wp_enqueue_script('housingEvents_editboxOpensectionsJS');	
		
	// Prints the Event details box content.
	function housing_events_inner_edit_box($post) 
	{
		// Add an nonce field so we can check for it later.
		wp_nonce_field(plugin_basename(__FILE__), 'housing_events_inner_edit_box_nonce' );
		
		global $post;
		$post_id = $post->ID;
 	
		$event_type = get_post_meta($post_id, 'event_type', true);
		
		$move_type = get_post_meta($post_id, 'move_type', true);
		
		$building_type = get_post_meta($post_id, 'building_type', true);
		$building_name = get_post_meta($post_id, 'building_name', true);
		
		$lot_type = get_post_meta($post_id, 'lot_type', true);
		$lot_name = get_post_meta($post_id, 'lot_name', true);
		
		$movebreakdown_type = get_post_meta($post_id, 'movebreakdown_type', true);
		$floor_name = get_post_meta($post_id, 'floor_name', true);
		
		$date_type = get_post_meta($post_id, 'date_type', true);
		
		$date_hour = get_post_meta($post_id, 'date_hour', true);
		$date_minute = get_post_meta($post_id, 'date_minute', true);
		$date_ampm = get_post_meta($post_id, 'date_ampm', true);
		$date_month = get_post_meta($post_id, 'date_month', true);
		$date_day = get_post_meta($post_id, 'date_day', true);
		$date_year = get_post_meta($post_id, 'date_year', true);
		
		$datespan_starthour = get_post_meta($post_id, 'datespan_starthour', true);
		$datespan_startminute = get_post_meta($post_id, 'datespan_startminute', true);
		$datespan_startampm = get_post_meta($post_id, 'datespan_startampm', true);
		$datespan_startmonth = get_post_meta($post_id, 'datespan_startmonth', true);
		$datespan_startday = get_post_meta($post_id, 'datespan_startday', true);
		$datespan_startyear = get_post_meta($post_id, 'datespan_startyear', true);
		$datespan_endhour = get_post_meta($post_id, 'datespan_endhour', true);
		$datespan_endminute = get_post_meta($post_id, 'datespan_endminute', true);
		$datespan_endampm = get_post_meta($post_id, 'datespan_endampm', true);
		$datespan_endmonth = get_post_meta($post_id, 'datespan_endmonth', true);
		$datespan_endday = get_post_meta($post_id, 'datespan_endday', true);
		$datespan_endyear = get_post_meta($post_id, 'datespan_endyear', true);
		
		$event_date_months = array('','January','February','March','April','May','June','July','August','September','October','November','December');
		$tcnj_buildings = array('Allen','Brewster','Centennial','College Houses','Cromwell','Decker','Ely','Eickhoff','Hausdoerffer','New Residence','Phelps','Townhouses East','Townhouses South','Townhouses West','Travers','Wolfe');
		?>
		
        <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>	
        <div style="margin-bottom: 9px">
        	<div style="display: inline-block; margin: 4.5px 0px; width: 25%"><strong>Event type:</strong></div>
            <span>
				<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="event-type" onchange="openEvent('general')" value="general" <?php if($event_type=='general'){echo 'checked';} ?>>General 
				<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="event-type" onchange="openEvent('move')" value="move" <?php if($event_type=='move'){echo 'checked';} ?>>Move-in/out
			</span>
        </div>
        <div id="event-move" style="display: <?php if($event_type=='move'){echo 'inherit';}else{echo 'none';} ?>">
			<hr/>
            <div style="margin-bottom: 9px">
            	<div style="display: inline-block; margin: 4.5px 0px; width: 25%"><strong>Move type:</strong></div>
				<span>
            		<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="move-type" value="movein" <?php if($move_type=='movein'){echo 'checked';} ?>>Move-in 
					<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="move-type" value="moveout" <?php if($move_type=='moveout'){echo 'checked';} ?>>Move-out
				</span>
            </div>
            <hr/>
            <div style="margin-bottom: 9px">
            	<div style="display: inline-block; margin: 4.5px 0px; width: 25%"><strong>Buildings:</strong></div>
				<span>
            		<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="building-type" onchange="openBuildings('one')" value="one" <?php if($building_type=='one'){echo 'checked';} ?>>One 
					<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="building-type" onchange="openBuildings('multi')" value="multi" <?php if($building_type=='multi'){echo 'checked';} ?>>Multi 
					<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="building-type" onchange="openBuildings('custom')" value="custom" <?php if($building_type=='custom'){echo 'checked';} ?>>Custom
				</span>
            </div>
            <div id="building-type_one" style="display: <?php if($building_type=='one'){echo 'inherit';}else{echo 'none';} ?>">
				<div style="display: inline-block; vertical-align: middle; width: 25%"><strong>Building Name:</strong></div>
				<select style="margin: 0px 4.5px 4.5px 12px" name="buildingtype-one_name">
		<?php
		$x = 0;
		while($tcnj_buildings[$x])
		{
			if($building_type=='one' && $tcnj_buildings[$x]==$building_name)
			{
				echo '<option value="'.esc_attr($tcnj_buildings[$x]).'" selected>'.esc_attr($tcnj_buildings[$x]).'</option>';
			}
			else
			{
				echo '<option value="'.esc_attr($tcnj_buildings[$x]).'">'.esc_attr($tcnj_buildings[$x]).'</option>';
			}
			$x++;
		}
		?>
	            </select>
			</div>
            <div id="building-type_multi" style="display: <?php if($building_type=='multi'){echo 'inherit';}else{echo 'none';} ?>">
				<div style="display: inline-block; vertical-align: top; width: 25%"><strong>Building Names:</strong></div>
				<div style="display: inline-block">
		<?php	
		$x = 0;
		while($tcnj_buildings[$x])
		{
			if($building_type=='multi' && in_array($tcnj_buildings[$x], $building_name))
			{
				echo '	<input style="margin-bottom: 4.5px; margin-left: 12px; margin-right: 4.5px" type="checkbox" name="buildingtype-multi_names[]" value="'.esc_attr($tcnj_buildings[$x]).'" checked>'.esc_attr($tcnj_buildings[$x]);
			}
			else
			{
				echo '	<input style="margin-bottom: 4.5px; margin-left: 12px; margin-right: 4.5px" type="checkbox" name="buildingtype-multi_names[]" value="'.esc_attr($tcnj_buildings[$x]).'">'.esc_attr($tcnj_buildings[$x]);
			}
			if(($x+1)%3==0){echo '<br/>';}
			$x++;
		}
		?>
				</div>
          	</div>
            <div id="building-type_custom" style="display: <?php if($building_type=='custom'){echo 'inherit';}else{echo 'none';} ?>">
				<div style="display: inline-block; width: 25%"><strong>Custom Building:</strong></div>
				<input style="margin-left: 11px" type="text" name="buildingtype-custom_name" placeholder="<?php if($building_type!='custom'){echo 'Custom building name';} ?>" value="<?php if($building_type=='custom'){echo $building_name;} ?>">
			</div>
            <hr/>
            <div style="margin-bottom: 9px">
            	<div style="display: inline-block; margin: 4.5px 0px; width: 25%"><strong>Lots:</strong></div>
				<span>
            		<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="lot-type" onchange="openLots('one')" value="one" <?php if($lot_type=='one'){echo 'checked';} ?>>One 
					<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="lot-type" onchange="openLots('multi')" value="multi" <?php if($lot_type=='multi'){echo 'checked';} ?>>Multi 
					<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="lot-type" onchange="openLots('custom')" value="custom" <?php if($lot_type=='custom'){echo 'checked';} ?>>Custom
				</span>
           	</div>
            <div id="lot-type_one" style="display: <?php if($lot_type=='one'){echo 'inherit';}else{echo 'none';} ?>">
                <div style="display: inline-block; width: 25%"><strong>Lot Name:</strong></div>
                <select style="margin: 0px 4.5px 4.5px 12px" name="lottype-one_name">
                	<option value="lot-5" <?php if($lot_type=='one' && $lot_name=='lot-5'){echo 'selected';} ?>>Lot 5</option>
                    <option value="lot-6" <?php if($lot_type=='one' && $lot_name=='lot-6'){echo 'selected';} ?>>Lot 6</option>
                    <option value="lot-8" <?php if($lot_type=='one' && $lot_name=='lot-8'){echo 'selected';} ?>>Lot 8*</option>
                    <option value="lot-9" <?php if($lot_type=='one' && $lot_name=='lot-9'){echo 'selected';} ?>>Lot 9**</option>
              	</select>
                <div style="margin-left: 25%"><span style="margin-left: 16.5px">* T/W Garage. ** Cromwell/Decker Garage.</span></div>
            </div>
            <div id="lot-type_multi" style="display: <?php if($lot_type=='multi'){echo 'inherit';}else{echo 'none';} ?>">
                <div style="display: inline-block; width: 25%"><strong>Lot Names:</strong></div>
                <span>
            		<input style="margin: 0px 4.5px 4.5px 12px" type="checkbox" name="lottype-multi_names[]" value="lot-5" <?php if($lot_type=='multi' && in_array('lot-5', $lot_name)){echo 'checked';} ?>>Lot 5 
					<input style="margin: 0px 4.5px 4.5px 12px" type="checkbox" name="lottype-multi_names[]" value="lot-6" <?php if($lot_type=='multi' && in_array('lot-6', $lot_name)){echo 'checked';} ?>>Lot 6 
					<input style="margin: 0px 4.5px 4.5px 12px" type="checkbox" name="lottype-multi_names[]" value="lot-8" <?php if($lot_type=='multi' && in_array('lot-8', $lot_name)){echo 'checked';} ?>>Lot 8*
                    <input style="margin: 0px 4.5px 4.5px 12px" type="checkbox" name="lottype-multi_names[]" value="lot-9" <?php if($lot_type=='multi' && in_array('lot-9', $lot_name)){echo 'checked';} ?>>Lot 9**
				</span>
                <div style="margin-left: 25%"><span style="margin-left: 16.5px">* T/W Garage. ** Cromwell/Decker Garage.</span></div>
            </div>
            <div id="lot-type_custom" style="display: <?php if($lot_type=='custom'){echo 'inherit';}else{echo 'none';} ?>">
				<div style="display: inline-block; width: 25%"><strong>Custom Lot:</strong></div>
				<input style="margin-left: 11px" type="text" name="lottype-custom_name" placeholder="<?php if($lot_type!='custom'){echo 'Custom lot name';} ?>"  value="<?php if($lot_type=='custom'){echo $lot_name;} ?>">
			</div>
            <hr/>
            <div style="margin-bottom: 9px">
            	<div style="display: inline-block; margin: 4.5px 0px; width: 25%"><strong>Move breakdown:</strong></div>
				<span>
            		<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="movebreakdown-type" onchange="openMoveBreakdown('floor')" value="floor" <?php if($movebreakdown_type=='floor'){echo 'checked';} ?>>By floor 
					<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="movebreakdown-type" onchange="openMoveBreakdown('building')" value="building" <?php if($movebreakdown_type=='building'){echo 'checked';} ?>>Building 
				</span>
            </div>
            <div id="movebreakdown-type_floor" style="display: <?php if($movebreakdown_type=='floor'){echo 'inherit';}else{echo 'none';} ?>">
            	<div style="display: inline-block; vertical-align: top; width: 25%"><strong>Selected floors:</strong></div>
				<div style="display: inline-block">
		<?php	
		$x = 1;
		while($x<=12)
		{
			if($movebreakdown_type=='floor' && in_array($x, $floor_name))
			{
				echo '	<input style="margin-bottom: 4.5px; margin-left: 12px; margin-right: 4.5px" type="checkbox" name="movebreakdowntypefloor_names[]" value="'.esc_attr($x).'" checked>Floor '.esc_attr($x);
			}
			else
			{
				echo '	<input style="margin-bottom: 4.5px; margin-left: 12px; margin-right: 4.5px" type="checkbox" name="movebreakdowntypefloor_names[]" value="'.esc_attr($x).'">Floor '.esc_attr($x);
			}
			if(($x)%4==0){echo '<br/>';}
			$x++;
		}
		?>
				</div>
			</div>
       	</div>
        <hr/>
        <div>
            <div style="margin-bottom: 9px">
            	<div style="display: inline-block; margin: 4.5px 0px; width: 25%"><strong>Date type:</strong></div>
				<span>
            		<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="date-type" onchange="openDateType('date')" value="date" <?php if($date_type=='date'){echo 'checked';} ?>>Date
					<input style="margin: 0px 4.5px 4.5px 12px" type="radio" name="date-type" onchange="openDateType('datespan')" value="datespan" <?php if($date_type=='datespan'){echo 'checked';} ?>>Datespan
				</span>
            </div>
            <div id="date-type_date" style="display: <?php if($date_type=='date'){echo 'inherit';}else{echo 'none';} ?>">
            	<div>
            		<div style="display: inline-block; width: 25%"><strong>Event time:</strong></div>
					<select style="margin: 0px 4.5px 4.5px 12px" name="datetype-date_hour">
      	<?php
        $x = 0;
		while($x<=12)
		{
			if($x==0){echo '<option>HH</option>';}
			else
			{
				if($date_type=='date' && $date_hour==$x){echo '<option value="'.esc_attr($x).'" selected>'.esc_attr($x).'</option>';}
				else{echo '<option value="'.esc_attr($x).'">'.esc_attr($x).'</option>';}
			}
			$x++;
		}
		?>
		       	 	</select>:
              		<select style="margin: 0px 4.5px 4.5px 0px" name="datetype-date_minute">
      	<?php
        $x = 0;
		while($x<=59)
		{
			if($x==0)
			{
				echo '<option>MM</option>';
				
			}
			
				if($x<=9)
				{
					if($date_type=='date' && $date_minute==$x)
					{
						if($x==0){if(is_numeric($date_minute)){echo '<option value="0" selected>00</option>';}else{echo '<option value="0">00</option>';}}
						else{echo '<option value="'.esc_attr($x).'" selected>0'.esc_attr($x).'</option>';}
					}
					else{echo '<option value="'.esc_attr($x).'">0'.esc_attr($x).'</option>';}
				}
				else
				{
					if($date_type=='date' && $date_minute==$x){echo '<option value="'.esc_attr($x).'" selected>'.esc_attr($x).'</option>';}
					else{echo '<option value="'.esc_attr($x).'">'.esc_attr($x).'</option>';}
				}
			
			$x++;
		}
		?>
                    </select>
                    <select style="margin: 0px 4.5px 4.5px 9px" name="datetype-date_ampm">
                        <option>AM/PM</option>
                        <option value="am" <?php if($date_type=='date' && $date_ampm=='am'){echo 'selected';} ?>>am</option>
                        <option value="pm" <?php if($date_type=='date' && $date_ampm=='pm'){echo 'selected';} ?>>pm</option>
                    </select>
            	</div>
                <div>
            		<div style="display: inline-block; width: 25%"><strong>Event date:</strong></div>
                    <select style="margin: 0px 4.5px 4.5px 12px" name="datetype-date_month">
      	<?php
        $x = 0;
		while($x<=12)
		{
			if($x==0){echo '<option>MONTH</option>';}
			else
			{
				if($date_type=='date' && $date_month==$x){echo '<option value="'.esc_attr($x).'" selected>'.esc_attr($event_date_months[$x]).'</option>';}
				else{echo '<option value="'.esc_attr($x).'">'.esc_attr($event_date_months[$x]).'</option>';}
			}
			$x++;
		}
		?>
		       	 	</select>
                    <select style="margin: 0px 4.5px 4.5px 0px" name="datetype-date_day">
      	<?php
        $x = 0;
		while($x<=31)
		{
			if($x==0){echo '<option>DD</option>';}
			if($date_type=='date' && $date_day==$x){echo '<option value="'.esc_attr($x).'" selected>'.esc_attr($x).'</option>';}
			else{echo '<option value="'.esc_attr($x).'">'.esc_attr($x).'</option>';}
			$x++;
		}
		?>
        			</select>
              		<select style="margin: 0px 4.5px 4.5px 0px" name="datetype-date_year">
      	<?php
        $x = 0; 
		$year = date("Y")-1;
		while($x<=5)
		{
			if($x==0){echo '<option>YEAR</option>';}
			if($date_type=='date' && $date_year==$year){echo '<option value="'.esc_attr($year).'" selected>'.esc_attr($year).'</option>';}
			else{echo '<option value="'.esc_attr($year).'">'.esc_attr($year).'</option>';}
			$year++;
			$x++;
		}
		?>
        			</select>
                </div>
            </div>
            <div id="date-type_datespan" style="display: <?php if($date_type=='datespan'){echo 'inherit';}else{echo 'none';} ?>">
               	<div>
            		<div style="display: inline-block; width: 25%"><strong>Start time:</strong></div>
					<select style="margin: 0px 4.5px 4.5px 12px" name="datetype-datespan_starthour">
      	<?php
        $x = 0;
		while($x<=12)
		{
			if($x==0){echo '<option>HH</option>';}
			if($date_type=='datespan' && $datespan_starthour==$x){echo '<option value="'.esc_attr($x).'" selected>'.esc_attr($x).'</option>';}
			else{echo '<option value="'.esc_attr($x).'">'.esc_attr($x).'</option>';}
			$x++;
		}
		?>
		       	 	</select>:
              		<select style="margin: 0px 4.5px 4.5px 0px" name="datetype-datespan_startminute">
      	<?php
        $x = 0;
		while($x<=59)
		{
			if($x==0){echo '<option>MM</option>';}
			if($x<=9)
			{
				if($date_type=='datespan' && $datespan_startminute==$x){echo '<option value="'.esc_attr($x).'" selected>0'.esc_attr($x).'</option>';}
				else{echo '<option value="'.esc_attr($x).'">0'.esc_attr($x).'</option>';}
			}
			else
			{
				if($date_type=='datespan' && $datespan_startminute==$x){echo '<option value="'.esc_attr($x).'" selected>'.esc_attr($x).'</option>';}
				else{echo '<option value="'.esc_attr($x).'">'.esc_attr($x).'</option>';}
			}
			$x++;
		}
		?>
                    </select>
                    <select style="margin: 0px 4.5px 4.5px 9px" name="datetype-datespan_startampm">
                        <option>AM/PM</option>
                        <option value="am" <?php if($date_type=='datespan' && $datespan_startampm=='am'){echo 'selected';} ?>>am</option>
                        <option value="pm" <?php if($date_type=='datespan' && $datespan_startampm=='pm'){echo 'selected';} ?>>pm</option>
                    </select>
            	</div>
                <div style="margin-bottom: 9px">
            		<div style="display: inline-block; width: 25%"><strong>Start date:</strong></div>
                    <select style="margin: 0px 4.5px 4.5px 12px" name="datetype-datespan_startmonth">
      	<?php
        $x = 0;
		while($x<12)
		{
			if($x==0){echo '<option>MONTH</option>';}
			if($date_type=='datespan' && $datespan_startmonth==$x){echo '<option value="'.esc_attr($x+1).'" selected>'.esc_attr($event_date_months[$x]).'</option>';}
			else{echo '<option value="'.esc_attr($x+1).'">'.esc_attr($event_date_months[$x]).'</option>';}
			$x++;
		}
		?>
		       	 	</select>
                    <select style="margin: 0px 4.5px 4.5px 0px" name="datetype-datespan_startday">
      	<?php
        $x = 0;
		while($x<=31)
		{
			if($x==0){echo '<option>DD</option>';}
			if($date_type=='datespan' && $datespan_startday==$x){echo '<option value="'.esc_attr($x).'" selected>'.esc_attr($x).'</option>';}
			else{echo '<option value="'.esc_attr($x).'">'.esc_attr($x).'</option>';}
			$x++;
		}
		?>
        			</select>
              		<select style="margin: 0px 4.5px 4.5px 0px" name="datetype-datespan_startyear">
      	<?php
        $x = 0; 
		$year = date("Y")-1;
		while($x<=5)
		{
			if($x==0){echo '<option>YEAR</option>';}
			if($date_type=='datespan' && $datespan_startyear==$year){echo '<option value="'.esc_attr($year).'" selected>'.esc_attr($year).'</option>';}
			else{echo '<option value="'.esc_attr($year).'">'.esc_attr($year).'</option>';}
			$year++;
			$x++;
		}
		?>
        			</select>
                </div>
                <div>
            		<div style="display: inline-block; width: 25%"><strong>End time:</strong></div>
					<select style="margin: 0px 4.5px 4.5px 12px" name="datetype-datespan_endhour">
      	<?php
        $x = 0;
		while($x<=12)
		{
			if($x==0){echo '<option>HH</option>';}
			if($date_type=='datespan' && $datespan_endhour==$x){echo '<option value="'.esc_attr($x).'" selected>'.esc_attr($x).'</option>';}
			else{echo '<option value="'.esc_attr($x).'">'.esc_attr($x).'</option>';}
			$x++;
		}
		?>
		       	 	</select>:
              		<select style="margin: 0px 4.5px 4.5px 0px" name="datetype-datespan_endminute">
      	<?php
        $x = 0;
		while($x<=59)
		{
			if($x==0){echo '<option>MM</option>';}
			if($x<=9)
			{
				if($date_type=='datespan' && $datespan_endminute==$x){echo '<option value="'.esc_attr($x).'" selected>0'.esc_attr($x).'</option>';}
				else{echo '<option value="'.esc_attr($x).'">0'.esc_attr($x).'</option>';}
			}
			else
			{
				if($date_type=='datespan' && $datespan_endminute==$x){echo '<option value="'.esc_attr($x).'" selected>'.esc_attr($x).'</option>';}
				else{echo '<option value="'.esc_attr($x).'">'.esc_attr($x).'</option>';}
			}
			$x++;
		}
		?>
                    </select>
                    <select style="margin: 0px 4.5px 4.5px 9px" name="datetype-datespan_endampm">
                        <option>AM/PM</option>
                        <option value="am" <?php if($date_type=='datespan' && $datespan_endampm=='am'){echo 'selected';} ?>>am</option>
                        <option value="pm" <?php if($date_type=='datespan' && $datespan_endampm=='pm'){echo 'selected';} ?>>pm</option>
                    </select>
            	</div>
                <div>
            		<div style="display: inline-block; width: 25%"><strong>End date:</strong></div>
                    <select style="margin: 0px 4.5px 4.5px 12px" name="datetype-datespan_endmonth">
      	<?php
        $x = 0;
		while($x<12)
		{
			if($x==0){echo '<option>MONTH</option>';}
			if($date_type=='datespan' && $datespan_endmonth==$x){echo '<option value="'.esc_attr($x+1).'" selected>'.esc_attr($event_date_months[$x]).'</option>';}
			else{echo '<option value="'.esc_attr($x+1).'">'.esc_attr($event_date_months[$x]).'</option>';}
			$x++;
		}
		?>
		       	 	</select>
                    <select style="margin: 0px 4.5px 4.5px 0px" name="datetype-datespan_endday">
      	<?php
        $x = 0;
		while($x<=31)
		{
			if($x==0){echo '<option>DD</option>';}
			if($date_type=='datespan' && $datespan_endday==$x){echo '<option value="'.esc_attr($x).'" selected>'.esc_attr($x).'</option>';}
			else{echo '<option value="'.esc_attr($x).'">'.esc_attr($x).'</option>';}
			$x++;
		}
		?>
        			</select>
              		<select style="margin: 0px 4.5px 4.5px 0px" name="datetype-datespan_endyear">
      	<?php
        $x = 0; 
		$year = date("Y")-1;
		while($x<=5)
		{
			if($x==0){echo '<option>YEAR</option>';}
			if($date_type=='datespan' && $datespan_endyear==$year){echo '<option value="'.esc_attr($year).'" selected>'.esc_attr($year).'</option>';}
			else{echo '<option value="'.esc_attr($year).'">'.esc_attr($year).'</option>';}
			$year++;
			$x++;
		}
		?>
        			</select>
                </div>
            </div>
		</div>
        <?php
	}
	
	// Save the Event details data
	function housing_events_save_data($id) 
	{
		global $post;
		$post_id = $GLOBALS['post']->ID;
		
		/* --- security verification --- */
		if(!wp_verify_nonce($_POST['housing_events_inner_edit_box_nonce'], plugin_basename(__FILE__))){return $id;}
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return $id;}
  		if('page' == $_POST['post_type']){if(!current_user_can('edit_page', $id)){return $id;}} 
		else{if(!current_user_can('edit_page', $id)){return $id;}}
		/* - end security verification - */
		
		if($_POST['event-type']!=null)
		{			
			$data = $_POST['event-type'];		
			update_post_meta($id, 'event_type', $data);
		}
		
		if($_POST['move-type']!=null)
		{			
			$data = $_POST['move-type'];		
			update_post_meta($id, 'move_type', $data);
		
			if($_POST['building-type']!=null)
			{	
				$data = $_POST['building-type'];	
				update_post_meta($id, 'building_type', $data);
				
					 if($_POST['building-type']=='one' && $_POST['buildingtype-one_name']!=null){$data = $_POST['buildingtype-one_name'];	update_post_meta($id, 'building_name', $data);}
				else if($_POST['building-type']=='multi')
				{
					$x = 0;
					$data = array();
					foreach($_POST['buildingtype-multi_names'] as $selected_building) 
					{
            			$data[$x] = $selected_building;
						$x++;
					}
					
					update_post_meta($id, 'building_name', $data);
				}
				else if($_POST['building-type']=='custom' && $_POST['buildingtype-custom_name']!=null){$data = $_POST['buildingtype-custom_name'];	update_post_meta($id, 'building_name', $data);}
			}
			
			if($_POST['lot-type']!=null)
			{	
				$data = $_POST['lot-type'];	
				update_post_meta($id, 'lot_type', $data);
				
					 if($_POST['lot-type']=='one' && $_POST['lottype-one_name']!=null){$data = $_POST['lottype-one_name'];	update_post_meta($id, 'lot_name', $data);}
				else if($_POST['lot-type']=='multi')
				{
					$x = 0;
					$data = array();
					foreach($_POST['lottype-multi_names'] as $selected_lot) 
					{
            			$data[$x] = $selected_lot;
						$x++;
					}
					
					update_post_meta($id, 'lot_name', $data);
				}
				else if($_POST['lot-type']=='custom' && $_POST['lottype-custom_name']!=null){$data = $_POST['lottype-custom_name'];	update_post_meta($id, 'lot_name', $data);}
			}
			
			if($_POST['movebreakdown-type']!=null)
			{			
				$data = $_POST['movebreakdown-type'];		
				update_post_meta($id, 'movebreakdown_type', $data);
				
				if($_POST['movebreakdown-type']=='floor')
				{
					$x = 0;
					$data = array();
					foreach($_POST['movebreakdowntypefloor_names'] as $selected_floor) 
					{
            			$data[$x] = $selected_floor;
						$x++;
					}
					
					update_post_meta($id, 'floor_name', $data);
				}
			}
		}
		
		if($_POST['date-type']!=null)
		{			
			$data = $_POST['date-type'];	
			update_post_meta($id, 'date_type', $data);
			
			if($_POST['date-type']=='date')
			{
				$data = $_POST['datetype-date_hour'];	update_post_meta($id, 'date_hour', $data);
				$data = $_POST['datetype-date_minute'];	update_post_meta($id, 'date_minute', $data);
				$data = $_POST['datetype-date_ampm'];	update_post_meta($id, 'date_ampm', $data);
				$data = $_POST['datetype-date_minute'];	update_post_meta($id, 'date_minute', $data);
				$data = $_POST['datetype-date_month'];	update_post_meta($id, 'date_month', $data);
				$data = $_POST['datetype-date_day'];	update_post_meta($id, 'date_day', $data);
				$data = $_POST['datetype-date_year'];	update_post_meta($id, 'date_year', $data);
			}
			else if($_POST['date-type']=='datespan')
			{
				$data = $_POST['datetype-datespan_starthour'];		update_post_meta($id, 'datespan_starthour', $data);
				$data = $_POST['datetype-datespan_startminute'];	update_post_meta($id, 'datespan_startminute', $data);
				$data = $_POST['datetype-datespan_startampm'];		update_post_meta($id, 'datespan_startampm', $data);
				$data = $_POST['datetype-datespan_startmonth'];		update_post_meta($id, 'datespan_startmonth', $data);
				$data = $_POST['datetype-datespan_startday'];		update_post_meta($id, 'datespan_startday', $data);
				$data = $_POST['datetype-datespan_startyear'];		update_post_meta($id, 'datespan_startyear', $data);
				
				$data = $_POST['datetype-datespan_endhour'];	update_post_meta($id, 'datespan_endhour', $data);
				$data = $_POST['datetype-datespan_endminute'];	update_post_meta($id, 'datespan_endminute', $data);
				$data = $_POST['datetype-datespan_endampm'];	update_post_meta($id, 'datespan_endampm', $data);
				$data = $_POST['datetype-datespan_endmonth'];	update_post_meta($id, 'datespan_endmonth', $data);
				$data = $_POST['datetype-datespan_endday'];		update_post_meta($id, 'datespan_endday', $data);
				$data = $_POST['datetype-datespan_endyear'];	update_post_meta($id, 'datespan_endyear', $data);
			}
		}
		
	}
	add_action('save_post', 'housing_events_save_data');
	
	//Events Widget Class
	class housing_events extends WP_Widget 
	{
		//Constructor
    	function housing_events() 
		{
        	parent::WP_Widget(false, $name = __('Events', 'wp_widget_plugin') );
    	}

		//Display widget
		function widget($args, $instance) 
		{
			wp_register_style('housingEvents_eventsCSS', plugins_url('/css/events.css', __FILE__), false, '1.0.0', 'all');
			wp_enqueue_style('housingEvents_eventsCSS');
			?>
            <style>
				.housing-event.event-container a.button[data-caltype="google"]:hover .icon,.housing-event.event-container a.button[data-caltype="google"]:active .icon{background-image: url('<?php echo plugins_url('icons/google_white-icon.png', __FILE__ ); ?>');}
				.housing-event.event-container a.button[data-caltype="apple"]:hover .icon,.housing-event.event-container a.button[data-caltype="apple"]:active .icon{background-image: url('<?php echo plugins_url('icons/apple_white-icon.png', __FILE__ ); ?>');}
				.housing-event.event-container a.button[data-caltype="android"]:hover .icon,.housing-event.event-container a.button[data-caltype="android"]:active .icon{background-image: url('<?php echo plugins_url('icons/android_white-icon.png', __FILE__ ); ?>');}
				.housing-event.event-container a.button[data-caltype="outlook"]:hover .icon,.housing-event.event-container a.button[data-caltype="outlook"]:active .icon{background-image: url('<?php echo plugins_url('icons/ms-outlook_white-icon.png', __FILE__ ); ?>');}
				.housing-event.event-container a.button[data-caltype="lightning"]:hover .icon,.housing-event.event-container a.button[data-caltype="thunderbird"]:active .icon{background-image: url('<?php echo plugins_url('icons/thunderbird_white-icon.png', __FILE__ ); ?>');}
			</style>
            <div class="housing-event event-container">
            	<div class="button-wrapper">
                	<a href="#" class="button" data-caltype="google" title="Google Calendar"><div class="icon" style="background-image: url('<?php echo plugins_url('icons/google_dark-icon.png', __FILE__ ); ?>')"></div><div class="title">Google Calendar</div></a>
                    <a href="#" class="button" data-caltype="apple" title="Apple iCalendar"><div class="icon" style="background-image: url('<?php echo plugins_url('icons/apple_dark-icon.png', __FILE__ ); ?>')"></div><div class="title">iCalendar</div></a>
                    <a href="#" class="button" data-caltype="android" title="Android aCalendar"><div class="icon" style="background-image: url('<?php echo plugins_url('icons/android_dark-icon.png', __FILE__ ); ?>')"></div><div class="title">aCalendar</div></a>
                    <a href="#" class="button" data-caltype="outlook" title="Microsoft Outlook"><div class="icon" style="background-image: url('<?php echo plugins_url('icons/ms-outlook_dark-icon.png', __FILE__ ); ?>')"></div><div class="title">MS Outlook</div></a>
                    <a href="#" class="button" data-caltype="thunderbird" title="Mozilla Thunderbird"><div class="icon" style="background-image: url('<?php echo plugins_url('icons/thunderbird_dark-icon.png', __FILE__ ); ?>')"></div><div class="title">Lightning Calendar</div></a>
                </div>
            </div>
            <?php
		}
	}	
	add_action('widgets_init', create_function('', 'return register_widget("housing_events");'));
?>