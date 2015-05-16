<?php
	/*
		Plugin Name: Housing - Contacts
		Plugin URI: http://housing.pages.tcnj.edu/
		Description: A plugin that manages and displays contact information.
		Version: 1.0.0
		Author: Patrick Kelly & Michael Young
		License: See readme.txt
	*/
	
	// Define Event post type
	function housing_contacts_init() 
	{
	  $labels = array(
		'name'               => 'Contacts',
		'singular_name'      => 'Contact',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Contact',
		'edit_item'          => 'Edit Contact',
		'new_item'           => 'New Contact',
		'all_items'          => 'All Contacts',
		'view_item'          => 'View Contact',
		'search_items'       => 'Search Contact',
		'not_found'          => 'No events found',
		'not_found_in_trash' => 'No events found in Trash',
		'parent_item_colon'  => '',
		'menu_name'          => 'Contacts'
	  );
	
	  $args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array('slug' => 'contact'),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 20,
		'supports'           => array('title','editor', 'thumbnail')
	  );
	
	  register_post_type('contact', $args);
	  flush_rewrite_rules();
	}
	add_action('init', 'housing_contacts_init');
	
	// Adds a box to the main column on the Event edit screens.
	function housing_contacts_edit_box() 
	{
		add_meta_box(
				'housing_contact_textdomain',
				__( 'Contact Details', 'housing_contacts_textdomain' ),
				'housing_contacts_inner_edit_box',
				'contact'
			);
	}
	add_action( 'add_meta_boxes', 'housing_contacts_edit_box' );
		
	/* Filter the single_template with our custom function*/
	function get_contact_single_template($single_template) {
    	global $post;
	    if ($post->post_type == 'contact') {
			$single_template = dirname( __FILE__ ) . '/single-contact.php';
			}
		return $single_template;
	}
	add_filter( 'single_template', 'get_contact_single_template' );
	
	// define custom archive template
	function get_housing_contact_archive_template( $archive_template ) {			 
    	if ( is_post_type_archive ( 'contact' ) ) {
    		include(dirname(__FILE__).'/archive-contact.php');
			exit();
		}
	}
	add_filter( 'archive_template', 'get_housing_contact_archive_template' );

	//Register Scripts
	wp_register_script('housingEvents_editboxOpensectionsJS', plugins_url('/js/editbox-opensections.js', __FILE__), false, '1.0.0', 'all');
			
	//Queue Scripts
	wp_enqueue_script('housingEvents_editboxOpensectionsJS');
	
	// change 'Enter title here' placeholder to 'Enter full name here'	
	function change_default_title( $title ){
		$screen = get_current_screen();
		if ( 'contact' == $screen->post_type ){
			$title = 'Enter full name here';
		}
		return $title;
	}
	add_filter( 'enter_title_here', 'change_default_title' );
	
	// change 'Featured Image' text
	function replace_featured_image_box() {
    	remove_meta_box( 'postimagediv', 'contact', 'side' );
    	add_meta_box('postimagediv', __('Contact Image'), 'post_thumbnail_meta_box', 'contact', 'side', 'low');
	}
	add_action( 'admin_head', 'replace_featured_image_box' );
	
	// change 'Set featured Image' text
	function custom_admin_post_thumbnail_html( $content ) {
		return $content = str_replace( __( 'featured' ), __( 'contact' ), $content); 
	}
	add_filter( 'admin_post_thumbnail_html', 'custom_admin_post_thumbnail_html' );
	
	// Prints the Event details box content.
	function housing_contacts_inner_edit_box($post) 
	{
		// Add an nonce field so we can check for it later.
		wp_nonce_field(plugin_basename(__FILE__), 'housing_contacts_inner_edit_box_nonce' );
		
		global $post;
		$post_id = $post->ID;
 	
		$contact_job_title = get_post_meta($post_id, 'contact_job_title', true);
		$contact_phone = get_post_meta($post_id, 'contact_phone', true);
		$contact_email = get_post_meta($post_id, 'contact_email', true);
		$contact_office = get_post_meta($post_id, 'contact_office', true);
		
		$tcnj_buildings = array('Allen','Brewster','Centennial','College Houses','Cromwell','Decker','Ely','Eickhoff','Hausdoerffer','New Residence','Phelps','Townhouses East','Townhouses South','Townhouses West','Travers','Wolfe');
		?>
		
        <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
        <div style="display: inherit'>
			<hr/>
            <div id="contact-job_title" style="display: inherit">
				<div style="display: inline-block; width: 25%"><strong>Job Title:</strong></div>
				<input style="margin-left: 11px" type="text" name="contact-job_title" placeholder="<?php if($building_type!='custom'){echo 'Custom building name';} ?>" value="<?php if($contact_job_title!=null){echo $contact_job_title;} ?>">
			</div>
            <div id="contact-phone" style="display: inherit">
				<div style="display: inline-block; width: 25%"><strong>Phone:</strong></div>
				<input style="margin-left: 11px" type="text" name="contact-phone" placeholder="ex. 000-000-0000" value="<?php if($contact_phone!=null){echo $contact_phone;} ?>">
			</div>
            <div id="contact-email" style="display: inherit">
				<div style="display: inline-block; width: 25%"><strong>Email:</strong></div>
				<input style="margin-left: 11px" type="text" name="contact-email" placeholder="example@tcnj.edu" value="<?php if($contact_email!=null){echo $contact_email;} ?>">
			</div>
            <div id="contact-office" style="display: inherit">
				<div style="display: inline-block; width: 25%"><strong>Office:</strong></div>
				<input style="margin-left: 11px" type="text" name="contact-office" placeholder="<?php if($building_type!='custom'){echo 'Custom building name';} ?>" value="<?php if($contact_office!=null){echo $contact_office;} ?>">
			</div>
       	</div>
        <?php
	}
	
	// Save the Event details data
	function housing_contacts_save_data($id) 
	{
		global $post;
		$post_id = $GLOBALS['post']->ID;
		
		/* --- security verification --- */
		if(!wp_verify_nonce($_POST['housing_contacts_inner_edit_box_nonce'], plugin_basename(__FILE__))){return $id;}
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return $id;}
  		if('page' == $_POST['post_type']){if(!current_user_can('edit_page', $id)){return $id;}} 
		else{if(!current_user_can('edit_page', $id)){return $id;}}
		/* - end security verification - */
		
		if($_POST['contact-job_title']!=null)
		{			
			update_post_meta($id, 'contact_job_title', $_POST['contact-job_title']);
		}
		if($_POST['contact-phone']!=null)
		{		
			$phone = $_POST['contact-phone'];
			if(preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phone)) {
				update_post_meta($id, 'contact_phone', $phone);
			}	
			else{
				wp_die("Phone numbers must use the following format: 000-000-0000");
			}
		}
		if($_POST['contact-email']!=null)
		{			
			$email = $_POST['contact-email'];
			$allowed = array('tcnj.edu');

			// Make sure the address is valid
			if (filter_var($email, FILTER_VALIDATE_EMAIL))
			{
			    $domain = array_pop(explode('@', $email));
			    if ( ! in_array($domain, $allowed))
			    {
			        wp_die("Only TCNJ email addresses are allowed.");
			    }
				else{
					update_post_meta($id, 'contact_email', $email);
				}
			}
			else{
				wp_die("Please enter a valid email address.  Make sure your email uses the following format: example@tcnj.edu");
			}
		}
		if($_POST['contact-office']!=null)
		{			
			update_post_meta($id, 'contact_office', $_POST['contact-office']);
		}
	}
	add_action('save_post', 'housing_contacts_save_data');
?>