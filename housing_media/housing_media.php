<?php
	/*
	Plugin Name: Housing - Media
	Plugin URI: http://housing.pages.tcnj.edu/
	Description: A plugin that displays photos, maps, panoramas, and YouTube videos.
	Version: 2.0.0
	Author: Patrick Kelly & Michael Young
	License: See readme.txt
	*/

	// edit boxes
 	// lighbox plugin editbox is handled by advanced custom fields, a separate plugin
	function mapsengine_edit_box(){add_meta_box('mapsengine_textdomain', __('Maps Engine', 'mapsengine_textdomain'), 'mapsengine_inner_edit_box', 'page');}
	function panoramas_edit_box(){add_meta_box('panoramas_textdomain', __('Panoramas', 'panoramas_textdomain'), 'panoramas_inner_edit_box', 'page');}
	function videos_edit_box(){add_meta_box('videos_textdomain', __('Videos', 'videos_textdomain'), 'videos_inner_edit_box', 'page');}
	
	// add input to pages with widget
	// lightbox plugin input added by advanced custom fields
	if(is_active_widget(false, false, 'mapsengine', true) && is_plugin_page()!='True'){add_action('add_meta_boxes', 'mapsengine_edit_box');}
	if(is_active_widget(false, false, 'panoramas', true) && is_plugin_page()!='True'){add_action('add_meta_boxes', 'panoramas_edit_box');}
	if(is_active_widget(false, false, 'videos', true) && is_plugin_page()!='True'){add_action('add_meta_boxes', 'videos_edit_box');}
	
	// register scripts
	wp_register_script('housing_bootstrapJS', plugins_url('/bootstrap/js/bootstrap.min.js', __FILE__), false, '1.0.0', 'all');
	wp_register_script('housing_mapsengine_editboxJS', plugins_url('/js/editbox_mapsengine.js', __FILE__), false, '1.0.0', 'all');
	wp_register_script('housing_modalcustomJS', plugins_url('/modal-custom/js/modal-custom.js', __FILE__), false, '1.0.0', 'all');
	wp_register_script('housing_panorama_editboxJS', plugins_url('/js/editbox_pano.js', __FILE__), false, '1.0.0', 'all');
	wp_register_script('housing_panorama_gyroJS', plugins_url('/js/pano2vrgyro.js', __FILE__), false, '1.0.0', 'all');
	wp_register_script('housing_panorama_panoJS', plugins_url('/js/pano2vr_player.js', __FILE__), false, '1.0.0', 'all');
	wp_register_script('housing_panorama_swfobjectJS', plugins_url('/js/swfobject.js', __FILE__), false, '1.0.0', 'all');
	wp_register_script('housing_panorama_switchJS', plugins_url('/js/panoswitch.js', __FILE__), false, '1.0.0', 'all');
	wp_register_script('housing_videos_editboxJS', plugins_url('/js/editbox_video.js', __FILE__), false, '1.0.0', 'all');
	
	// register styles
	wp_register_style('housing_bootstrapCSS', plugins_url('/bootstrap/css/bootstrap.css', __FILE__), false, '1.0.0', 'all');
	wp_register_style('housing_editboxCSS', plugins_url('/css/editbox.css', __FILE__), false, '1.0.0', 'all');
	wp_register_style('housing_modalcustomCSS', plugins_url('/modal-custom/css/modal-custom.css', __FILE__), false, '1.0.0', 'all');
	wp_register_style('housing_modalcustom_lightboxCSS', plugins_url('/modal-custom/css/modal-custom_lightbox.css', __FILE__), false, '1.0.0', 'all');
	wp_register_style('housing_modalcustom_mapsengineCSS', plugins_url('/modal-custom/css/modal-custom_mapsengine.css', __FILE__), false, '1.0.0', 'all');
	wp_register_style('housing_moduleCSS', plugins_url('/css/module.css', __FILE__), false, '1.0.0', 'all');
	
	include('php/housing_lightbox.php');
	include('php/housing_mapsengine.php');
	include('php/housing_panoramas.php');
	include('php/housing_videos.php');
?>