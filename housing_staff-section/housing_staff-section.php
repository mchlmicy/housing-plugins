<?php
/*
Plugin Name: Housing - Staff Section
Plugin URI: http://housing.pages.tcnj.edu/
Description: A plugin that password-protects (via CAS) a section of the TCNJ Housing website.
Version: 1.0.2
Author: Patrick Kelly & Michael Young
License: See readme.txt
*/

//flush rewrite rules if our rewrite rule isn't yet included
add_action('admin_init', 'handle_staff_route');
function handle_staff_route()
{
    flush_rewrite_rules();
}

// add 'router' variable to link 'staff' url to this plugins index.php
add_filter('init', 'declare_staff_vars');
function declare_staff_vars()
{
    add_rewrite_tag('%router%', '([a-z]+)');
}

// add 'staff' url

// add new post type to handle staff attachments
add_action( 'init', 'create_staff_post_type' );
function create_staff_post_type() {
  $labels = array(
    'name'               => 'Staff Pages',
    'singular_name'      => 'Staff Page',
    'add_new'            => 'Add New Page',
    'add_new_item'       => 'Add New Page',
    'edit_item'          => 'Edit Page',
    'new_item'           => 'New Page',
    'all_items'          => 'All Pages',
    'view_item'          => 'View Page',
    'search_items'       => 'Search pages',
    'not_found'          => 'No pages found',
    'not_found_in_trash' => 'No pages found in Trash',
    'parent_item_colon'  => '',
    'menu_name'          => 'Housing Staff'
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
	'exclude_from_search' => true,
    'query_var'          => true,
    'rewrite'            => array( 'slug' => 'staff' ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'supports'           => array( 'title', 'editor' )
  );
  register_post_type( 'staff_page', $args );
  add_action( 'admin_init', 'register_staff_users' );
}

function register_staff_users() {
	//register our settings
	register_setting( 'housing-staff-section-settings', 'users' );
}

// add sub-menu page to manage uploaded files in the staff section
add_action('admin_menu', 'register_staff_section_manage_users');
function register_staff_section_manage_users() {
	add_submenu_page( 'edit.php?post_type=staff_page', 'Manage Users', 'Manage Users', 'manage_options', 'staff-manage-users', 'staff_section_manage_users_page_callback' ); 
}

function staff_section_manage_users_page_callback() { ?>
	<div class="wrap"><div id="icon-tools" class="icon32"></div>
		<h2>Manage Users</h2>
			<form method="post" action="options.php">
    			<?php settings_fields( 'housing-staff-section-settings' ); ?>
    			<?php do_settings_sections( 'housing-staff-section-settings' ); ?>
        		<p>Please enter the UNIX usernames of the users you would like to add to the staff section.</p>
                <p><i>NOTE: Only one username is allowed per line.  Please do not use commas or spaces.</i></p>
        		<textarea name="users" value="<?php echo get_option('users'); ?>" rows="10" cols="70"><?php echo get_option('users'); ?></textarea>
    			<?php submit_button(); ?>
			</form>
	</div>
<?php
}

// add meta box for file upload
add_action( 'add_meta_boxes', 'staff_file_upload_edit_box' );
function staff_file_upload_edit_box() {
	add_meta_box(
            'staff_file_upload_textdomain',
            __( 'Upload a File', 'staff_file_upload_textdomain' ),
            'staff_file_upload_inner_edit_box',
            'staff_page'
        );
}

// populate the meta box with fields
function staff_file_upload_inner_edit_box($post) {

  // Add an nonce field so we can check for it later.
  wp_nonce_field(plugin_basename(__FILE__), 'staff_file_upload_inner_edit_box_nonce' );

  /*
   * Use get_post_meta() to retrieve an existing value
   * from the database and use the value for the form.
   */
  $file_path = get_post_meta( $post->ID, 'staff_file_upload', true );

  echo '<hr><p class="description">';
       _e( "Select a File to Upload", 'staff_file_upload_textdomain' );
  echo '</p> ';
  echo '<input type="file" id="staff_file_upload" name="staff_file_upload" value="' . esc_attr( $file_path['file'] ) . '" title="' . esc_attr( $file_path['url'] ) . '" size="25">';  
  if (isset($file_path['file'])){echo '<br><i>Currently '.$file_path['url'].'</i>';}
}
function staff_file_upload_save_data($id) {

	/* --- security verification --- */
	if(!wp_verify_nonce($_POST['staff_file_upload_inner_edit_box_nonce'], plugin_basename(__FILE__))) {
	  return $id;
	} // end if
	  
	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
	  return $id;
	} // end if
	  
	if('staff_page' == $_POST['post_type']) {
	  if(!current_user_can('edit_page', $id)) {
	    return $id;
	  } // end if
	} else {
   		if(!current_user_can('edit_page', $id)) {
	    	return $id;
	   	} // end if
	} // end if
	/* - end security verification - */
	
	// Make sure the file array isn't empty
	if(!empty($_FILES['staff_file_upload']['name'])) {
		
		// Use the WordPress API to upload the file
		$upload = wp_upload_bits($_FILES['staff_file_upload']['name'], null, file_get_contents($_FILES['staff_file_upload']['tmp_name']));
	
		if(isset($upload['error']) && $upload['error'] != 0) {
			wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
		} else {
			add_post_meta($id, 'staff_file_upload', $upload);
			update_post_meta($id, 'staff_file_upload', $upload);		
		}
	} else {
		wp_die("An error occured while uploading your file.  Please try again.");
	}
}
add_action('save_post', 'staff_file_upload_save_data');

function update_upload_form() {
    echo ' enctype="multipart/form-data"';
} // end update_edit_form
add_action('post_edit_form_tag', 'update_upload_form');

// add custom columns to 'All Files' page
add_filter( 'manage_edit-staff_page_columns', 'set_custom_edit_staff_page_columns' );
add_action( 'manage_staff_page_posts_custom_column' , 'custom_staff_page_column', 10, 2 );
// set the new column order
function set_custom_edit_staff_page_columns($columns) {
	return array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Title'),
        'file_description' => __('Description'),
        'file_path' =>__( 'File Path'),
		'date' => __('Date')
    );
}
// define the content for the new columns
function custom_staff_page_column( $column, $post_id ) {
    switch ( $column ) {
		case 'file_description' :
			echo the_content($post_id);
			break;
		case 'file_path' :
		    $column_file_path = get_post_meta( $post_id, 'staff_file_upload', true );
	    	?><a href="<?php echo $column_file_path['url']; ?>"><?php echo $column_file_path['url']; ?></a><?php
	    	break;
    }
}
// define a custom single template
function get_custom_post_type_template($single_template) {
     global $post;

     if ($post->post_type == 'staff_page') {
          $single_template = dirname( __FILE__ ) . '/single-staff_page.php';
     }
     return $single_template;
}

add_filter( 'single_template', 'get_custom_post_type_template' );
// define custom archive template
function get_staff_page_template( $archive_template ) {
	global $post;
	global $wp_query;
			 
    if ( is_post_type_archive ( 'staff_page' ) ) {
		require_once('config.php');
		require_once('CAS.php');
		require_once('CAS/CAS.php');

		phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);
		phpCAS::setNoCasServerValidation();
		$auth = phpCAS::checkAuthentication();
		if ($auth) {
			$username = phpCAS::getUser();
			$active_users = get_option('users');
			if (strpos($active_users,$username) !== false) {
    			return dirname(__FILE__).'/archive-staff_page.php';
			}
			else{
    			return dirname(__FILE__).'/404.php';
			}
		}
		else{
			phpCAS::forceAuthentication();
		}
    }
    return $archive_template;
}
add_filter( 'archive_template', 'get_staff_page_template' );
?>