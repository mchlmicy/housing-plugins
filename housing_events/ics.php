<?php
	// Get the post_id
	$post_id = $event_id;
	// buildings
	$building_type = get_post_meta($event_id, 'building_type', true);
	$building_name = get_post_meta($event_id, 'building_name', true);
	if($building_type && $building_name)
	{
		if($building_type=='one' || $building_type=='custom'){$location = $building_name;}
		else if($building_type=='multi'){$x = 0; while($x < count($building_name)){if($x>0){$location .= ', ';} $location .= $building_name[$x]; $x++;	}}
	}
	// determine whether single or multi-day event
	$date_type = get_post_meta($post_id, 'date_type', true);
	if($date_type == 'date')
	{	
		$date_hour = get_post_meta($post_id, 'date_hour', true);
		$date_minute = get_post_meta($post_id, 'date_minute', true);if(intval($date_minute)<=9){$date_minute = '0'.$date_minute;}
		$date_ampm = get_post_meta($post_id, 'date_ampm', true);
		$date_month = get_post_meta($post_id, 'date_month', true);
		$date_day = get_post_meta($post_id, 'date_day', true);
		$date_year = get_post_meta($post_id, 'date_year', true);
		
		if($date_hour && $date_minute && $date_ampm && $date_month && $date_day && $date_year)
		{
			if($date_ampm == 'pm' && $date_hour != 12){$date_hour = $date_hour+12;}
			$dt = new DateTime($date_year.'-'.$date_month.'-'.$date_day.' '.$date_hour.':'.$date_minute.':00');
			$start_date_string = $end_date_string = $dt->format('Ymd').'T'.$dt->format('His');
		}
	}
	else if($date_type == 'datespan')
	{
		$datespan_starthour = get_post_meta($post_id, 'datespan_starthour', true);
		$datespan_startminute = get_post_meta($post_id, 'datespan_startminute', true);if(intval($datespan_startminute)<=9){$datespan_startminute = '0'.$datespan_startminute;}
		$datespan_startampm = get_post_meta($post_id, 'datespan_startampm', true);
		$datespan_startmonth = get_post_meta($post_id, 'datespan_startmonth', true);
		$datespan_startday = get_post_meta($post_id, 'datespan_startday', true);
		$datespan_startyear = get_post_meta($post_id, 'datespan_startyear', true);
		$datespan_endhour = get_post_meta($post_id, 'datespan_endhour', true);
		$datespan_endminute = get_post_meta($post_id, 'datespan_endminute', true);if(intval($datespan_endminute)<=9){$datespan_endminute = '0'.$datespan_endminute;}
		$datespan_endampm = get_post_meta($post_id, 'datespan_endampm', true);
		$datespan_endmonth = get_post_meta($post_id, 'datespan_endmonth', true);
		$datespan_endday = get_post_meta($post_id, 'datespan_endday', true);
		$datespan_endyear = get_post_meta($post_id, 'datespan_endyear', true);
		
		if($datespan_starthour && $datespan_startminute && $datespan_startampm && $datespan_startmonth && $datespan_startday && $datespan_startyear)
		{
			if($datespan_startampm == 'pm' && $datespan_starthour != 12){$datespan_starthour = $datespan_starthour+12;}
			if($datespan_endampm == 'pm' && $datespan_endhour != 12){$datespan_endhour = $datespan_endhour+12;}

			$dtstart = new DateTime($datespan_startyear.'-'.$datespan_startmonth.'-'.$datespan_startday.' '.$datespan_starthour.':'.$datespan_startminute.':00');
			$dtend = new DateTime($datespan_endyear.'-'.$datespan_endmonth.'-'.$datespan_endday.' '.$datespan_endhour.':'.$datespan_endminute.':00');
			
			$start_date_string = $dtstart->format('Ymd').'T'.$dtstart->format('His');
			$end_date_string = $dtend->format('Ymd').'T'.$dtend->format('His');
		}	
	}

// iCal date format: yyyymmddThhiissZ
// PHP equiv format: Ymd\This

// Build the ics file
$ical = 'BEGIN:VCALENDAR
VERSION:2.0
CALSCALE:GREGORIAN
BEGIN:VEVENT
UID:' . md5($event['title']) . '
DTSTAMP:' . time() . '
DTSTART:' . $start_date_string . '
DTEND:' . $end_date_string . '
LOCATION:' . str_replace(",","\,",addslashes($location)) . '
DESCRIPTION:' . str_replace(",","\,",addslashes(get_post_field('post_content', $event_id))) . '
URL;VALUE=URI:' . get_permalink($event_id) . '
SUMMARY:' . str_replace(",","\,",addslashes(htmlspecialchars_decode(get_the_title($event_id)))) . '
END:VEVENT
END:VCALENDAR';

//set correct content-type-header
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=TCNJ-Event.ics');
echo $ical;
exit;
?>