<?php include('../../../resedhousing/wordpress/wp-blog-header.php'); ?>
<?php // Include WordPress 
	define('WP_USE_THEMES', false); require('../../../resedhousing/wordpress/wp-load.php'); 
	$id = mysql_real_escape_string($_GET['id']); 
	$src_tz = new DateTimeZone('America/New_York');
	$dest_tz = new DateTimeZone('UTC');
	$eventmeta = get_post_meta( $id, '' );
	
	$dt1 = new DateTime($eventmeta["eventdate"][0].' '.$eventmeta["eventtime"][0].':00', $src_tz);$dt1->setTimeZone($dest_tz);
	$dt2 = new DateTime($eventmeta["eventdate"][0].' '.$eventmeta["eventtime"][0].':00', $src_tz);$dt2->setTimeZone($dest_tz);
	$dt3 = new DateTime($eventmeta["eventdate"][0].' '.$eventmeta["eventend"][0].':00', $src_tz);$dt3->setTimeZone($dest_tz);
	$dt4 = new DateTime($eventmeta["eventdate"][0].' '.$eventmeta["eventend"][0].':00', $src_tz);$dt4->setTimeZone($dest_tz);
	
    $arr = array('id' => get_the_ID($id), 'title' => get_the_title($id), 'content' => strip_tags(get_post_field('post_content', $id)), 'date' => $dt1->format('Ymd'), 'start' => $dt2->format('His'), 'end' => $dt4->format('His'), 'end_date' => $dt3->format('Ymd'), 'location' => $eventmeta["eventlocation"][0]);

	echo json_encode($arr);
	?>