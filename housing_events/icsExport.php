<?php
// Fetch vars
$postedDetails = $_POST;
$array = explode(",",str_replace(array( "{", "}" ), "", $postedDetails['content']));
$array_id = explode(":",$array[0]);
$array_title = explode(":",$array[1]);
$array_summary = explode(":",$array[2]);
$array_location = explode(":",$array[3]);
$array_date = explode(":",$array[4]);
$array_time = explode(":",$array[5]);
$array_enddate = explode(":",$array[6]);
$array_endtime = explode(":",$array[7]);

$event = array("id" => $array_id[1], "title" => urldecode($array_title[1]), "description" => urldecode($array_summary[1]), "address" => urldecode($array_location[1]), "datestart" => $array_date[1].'T' . $array_time[1] . 'Z',"dateend" => $array_enddate[1].'T'.$array_endtime[1].'Z');

// iCal date format: yyyymmddThhiissZ
// PHP equiv format: Ymd\This

// Build the ics file
$ical = 'BEGIN:VCALENDAR
VERSION:2.0
CALSCALE:GREGORIAN
BEGIN:VEVENT
DTEND:' . $event['dateend'] . '
UID:' . md5($event['title']) . '
DTSTAMP:' . time() . '
LOCATION:' . str_replace(",","\,",addslashes(stripslashes(urldecode($array_location[1])))) . '
DESCRIPTION:' . str_replace(",","\,",addslashes($event['description'])) . '
URL;VALUE=URI:http://lionsmatter.com/resedhousing/news/events/?id=' . $event['id'] . '
SUMMARY:' . str_replace(",","\,",addslashes($event['title'])) . '
DTSTART:' . $event['datestart'] . '
END:VEVENT
END:VCALENDAR';
 
//set correct content-type-header
if($event['id']){
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=TCNJ-Event.ics');
echo $ical;
} else {
// If $id isn't set, then kick the user back to home. Do not pass go, and do not collect $200.
header('Location: /resedhousing/');
}
?>