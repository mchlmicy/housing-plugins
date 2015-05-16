<?php 
	// load WordPress header
	get_header();
	
	// query for all events
	query_posts(array('post_type' => array('event')));
	
	//Register Scripts
	wp_register_style('housingEvents_eventsCSS', plugins_url('/css/events.css', __FILE__), false, '1.0.0', 'all');
			
	//Queue Scripts
	wp_enqueue_style('housingEvents_eventsCSS');
	
	// Get the post_id
	global $post;
	$post_id = $post->ID;
	
	// Get the date information
	$date_type = get_post_meta($post_id, 'date_type', true);
	$date_months = array('','January','February','March','April','May','June','July','August','September','October','November','December');
	function date_day_suffix($day){if($day>3){return 'th';}else if($day == 1){return 'st';}else if($day == 2){return 'nd';}else if($day == 3){return 'rd';}}
	if($date_type == 'date')
	{	
		$date_hour = get_post_meta($post_id, 'date_hour', true);
		$date_minute = get_post_meta($post_id, 'date_minute', true);if(intval($date_minute)<=9){$date_minute = '0'.$date_minute;}
		$date_ampm = get_post_meta($post_id, 'date_ampm', true);
		$date_month = $date_months[get_post_meta($post_id, 'date_month', true)];
		$date_day = get_post_meta($post_id, 'date_day', true);
		$date_year = get_post_meta($post_id, 'date_year', true);
		
		if($date_hour && $date_minute && $date_ampm && $date_month && $date_day && $date_year)
		{
			if($date_year == date("Y")){$date_string = $date_month.' '.$date_day.date_day_suffix($date_day).' at '.$date_hour.':'.$date_minute.$date_ampm;}
			else{$date_string = $date_month.' '.$date_day.date_day_suffix($date_day).', '.$date_year.' at '.$date_hour.':'.$date_minute.$date_ampm;}
		}
	}
	else if($date_type == 'datespan')
	{
		$datespan_starthour = get_post_meta($post_id, 'datespan_starthour', true);
		$datespan_startminute = get_post_meta($post_id, 'datespan_startminute', true);if(intval($datespan_startminute)<=9){$datespan_startminute = '0'.$datespan_startminute;}
		$datespan_startampm = get_post_meta($post_id, 'datespan_startampm', true);
		$datespan_startmonth = $date_months[get_post_meta($post_id, 'datespan_startmonth', true)];
		$datespan_startday = get_post_meta($post_id, 'datespan_startday', true);
		$datespan_startyear = get_post_meta($post_id, 'datespan_startyear', true);
		$datespan_endhour = get_post_meta($post_id, 'datespan_endhour', true);
		$datespan_endminute = get_post_meta($post_id, 'datespan_endminute', true);if(intval($datespan_endminute)<=9){$datespan_endminute = '0'.$datespan_endminute;}
		$datespan_endampm = get_post_meta($post_id, 'datespan_endampm', true);
		$datespan_endmonth = $date_months[get_post_meta($post_id, 'datespan_endmonth', true)];
		$datespan_endday = get_post_meta($post_id, 'datespan_endday', true);
		$datespan_endyear = get_post_meta($post_id, 'datespan_endyear', true);
		
		if($datespan_starthour && $datespan_startminute && $datespan_startampm && $datespan_startmonth && $datespan_startday && $datespan_startyear)
		{
			if($datespan_startyear == $datespan_endyear && $datespan_startmonth == $datespan_endmonth && $datespan_startday == $datespan_endday && $datespan_endhour && $datespan_endminute && $datespan_endampm && $datespan_endmonth && $datespan_endday && $datespan_endyear)
			{
				if($datespan_endyear == date("Y")){$date_string = 'on '.$datespan_startmonth.' '.$datespan_startday.date_day_suffix($datespan_startday).' from '.$datespan_starthour.':'.$datespan_startminute.$datespan_startampm.' to '.$datespan_endhour.':'.$datespan_endminute.$datespan_endampm;}
				else{$date_string = 'on '.$datespan_startmonth.' '.$datespan_startday.date_day_suffix($datespan_startday).', '.$datespan_startyear.' from '.$datespan_starthour.':'.$datespan_startminute.$datespan_startampm.' to '.$datespan_endhour.':'.$datespan_endminute.$datespan_endampm;}
			}
			else
			{
				if($datespan_startyear == date("Y")){$date_string = $datespan_startmonth.' '.$datespan_startday.date_day_suffix($datespan_startday).' at '.$datespan_starthour.':'.$datespan_startminute.$datespan_startampm;}
				else{$date_string = $datespan_startmonth.' '.$datespan_startday.date_day_suffix($datespan_startday).', '.$datespan_startyear.' at '.$datespan_starthour.':'.$datespan_startminute.$datespan_startampm;}
				
				if($datespan_endhour && $datespan_endminute && $datespan_endampm && $datespan_endmonth && $datespan_endday && $datespan_endyear)
				{
					if($datespan_endyear == date("Y")){$date_string = 'from '.$date_string.' to '.$datespan_endmonth.' '.$datespan_endday.date_day_suffix($datespan_endday).' at '.$datespan_endhour.':'.$datespan_endminute.$datespan_endampm;}
					else{$date_string = 'from '.$date_string.' to '.$datespan_endmonth.' '.$datespan_endday.date_day_suffix($datespan_endday).', '.$datespan_endyear.' at '.$datespan_endhour.':'.$datespan_endminute.$datespan_endampm;}
				}
			}
		}	
	}
	
	?>
    <div class='housing-event single'>
    	<div class='header'><?php echo the_title(); ?></div>
		<div class="subheader">&mdash; <?php echo $date_string; ?></div>
        <?php 
			
			echo $post->post_content.'<br/><br/>';
			
			$event_type = get_post_meta($post_id, 'event_type', true);
			if($event_type == 'move')
			{	
				$lot_type = get_post_meta($post_id, 'lot_type', true);
				$lot_name = get_post_meta($post_id, 'lot_name', true);
				if($lot_type && $lot_name)
				{
					?><strong>Students <?php if($move_type=='movein'){echo 'moving in ';}else if($move_type=='moveout'){echo 'moving out ';} ?> should use <?php
					if($lot_type=='one'){echo 'Lot '.$lot_name;}
					else if($lot_type=='custom'){echo $lot_name;}
					else if($lot_type=='multi'){echo 'Lots '; $x = 0; while($x < count($lot_name)){if($x>0){echo ', ';} echo $lot_name[$x]; $x++;}}
					?>.</strong><br/><?php
				}
					
				$building_type = get_post_meta($post_id, 'building_type', true);
				$building_name = get_post_meta($post_id, 'building_name', true);
				if($building_type && $building_name)
				{
					?>Buildings affected: <?php
					if($building_type=='one' || $building_type=='custom'){echo $building_name;}
					else if($building_type=='multi'){$x = 0; while($x < count($building_name)){if($x>0){echo ', ';} echo $building_name[$x]; $x++;}}
					?><br/><?php
				}
				
				$movebreakdown_type = get_post_meta($post_id, 'movebreakdown_type', true);
				if($movebreakdown_type == 'floor'){$floor_name = get_post_meta($post_id, 'floor_name', true); echo '<div>Floors: '; $x = 0; while($x < count($floor_name)){if($x>0){echo ', ';} echo $floor_name[$x]; $x++;} echo '</div>';}
			}
        ?>
    </div>
    <?php
		
	// end query
	wp_reset_query();
	
	// load WordPress footer
	get_footer(); 
?>