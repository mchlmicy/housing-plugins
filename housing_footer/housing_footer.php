<?php
/*
	Plugin Name: Housing - Footer
	Plugin URI: http://housing.pages.tcnj.edu/
	Description: A plugin that displays a responsive footer.
	Version: 1.0.5
	Author: Patrick Kelly & Michael Young
	License: See readme.txt
*/

class footer extends WP_Widget 
{
	// constructor
    function footer() 
	{
        parent::WP_Widget(false, $name = __('Footer', 'wp_widget_plugin'));
    }

	function form($instance) 
	{
		$menus = get_nav_menu_locations();
				echo "	<p>Please select a navigation menu to use as the footer</p>
    				<label for='".$this->get_field_id("menu")."'>
    					<p>Menu:
							<select name='".$this->get_field_name("menu")."' id='".$this->get_field_id("menu") ."'>";
							foreach($menus as $menu => $value ):
								echo "<option value='".$value."'>
									".$menu."
								</option>";
							endforeach;
						echo "</select>
						</p>
    				</label>
				";
	}

	// widget update
	function update($new_instance, $old_instance) 
	{
		$instance =  $old_instance;
    	
		//update the menu
    	$instance['menu']=  $new_instance['menu'];
	    return $instance;
	}

	//Display widget
	function widget($args, $instance) 
	{
		//Register Styles
		wp_register_style('footerCSS', plugins_url('/css/footer.css', __FILE__), false, '1.0.0', 'all');
   		wp_register_style('footericonsCSS', plugins_url('/css/icons.css', __FILE__), false, '1.0.0', 'all');
		wp_register_style('elusiveiconfontCSS', plugins_url('/css/elusive-webfont.css', __FILE__), false, '1.0.0', 'all');
		
		//Register Scripts
		wp_register_script('footeraccordionJS', plugins_url('/js/accordion.js', __FILE__), array( 'jquery' ), '1.0.0', 'all');
		wp_register_script('footerbootstrapJS', plugins_url('/js/bootstrap.min.js', __FILE__), false, '1.0.0', 'all');

	
		//Queue Styles
		wp_enqueue_style('footerCSS');
		wp_enqueue_style('footericonsCSS');
		wp_enqueue_style('elusiveiconfontCSS');
		
		//Queue Scripts
		wp_enqueue_script('footeraccordionJS');

		$menu_id = $instance['menu'];
		$menu = wp_get_nav_menu_object($menu_id);
    	$menuitems = wp_get_nav_menu_items( $menu->term_id, array( 'order' => 'DESC' ) );

    	?>
		<!--Display the widget-->
        <footer class="footer">
			<div id="footer-sitemap_desktop"> 
				<div class="sitemap"> 
					<?php
                     	$count = 0;
                        $submenu = false;
            
                      	foreach($menuitems as $item ):
                        	// get page id from using menu item object id
                            $id = get_post_meta( $item->ID, '_menu_item_object_id', true );
                            
							// set up a page object to retrieve page data
                            $page = get_page( $id );
                            $link = get_page_link( $id );
        
                          	// item does not have a parent so menu_item_parent equals 0 (false)
                          	if(!$item->menu_item_parent)
							{ 
                                // save this id for later comparison with sub-menu items
                                $parent_id = $item->ID;
                       
                        		echo "	<div class='footer-column'> 
											<ul> 
												<li class='header'>
                                					<a href='".$link."'>".$page->post_title."</a>
                               					</li> 
									";
							}
							if($parent_id == $item->menu_item_parent)
							{
								if(!$submenu)
								{
									$submenu = true;
								}
				
								echo "			<li class='link'>
													<a href='".$link."' title='".$page->post_title."'>".$page->post_title."</a>
												</li>
									".$subparent_id;
							
			
							if(!$menuitems[$count + 1]->menu_item_parent && $submenu)
							{
								echo "</ul>";
								$submenu = false; 
							}}
							if(!$menuitems[$count + 1]->menu_item_parent)
							{
								echo "</div>";
								$submenu = false; 
							}
	
							$count++; 
						endforeach; 
					?>
                </div> 
			</div> 
			<div id="footer-sitemap_responsive">
				<div class="sitemap">
                	<?php
						$rcount = 0;
						$rsubmenu = false;
		
						foreach($menuitems as $item ):
							// get page id from using menu item object id
							$id = get_post_meta( $item->ID, '_menu_item_object_id', true );
							// set up a page object to retrieve page data
							$page = get_page( $id );
							$link = get_page_link( $id );
		
							// item does not have a parent so menu_item_parent equals 0 (false)
							if(!$item->menu_item_parent)
							{
								// save this id for later comparison with sub-menu items
								$parent_id = $item->ID;
									
								echo '	<div class="mobile-sitemap_title-container">
											<a class="accordion-toggle" style="color: #c0c0c0; href="#" onclick="toggleAccordion(\''. esc_attr($rcount) .'\')">
												'.$page->post_title.'
											</a>
										</div>
										<div id="footerdropdown-'.$rcount.'" class="collapse footer-dropdown">
											<ul>
												<li>
													<a href="'.$link.'"><b>'.$page->post_title.'</b></a>
									';
							}
							else if($parent_id == $item->menu_item_parent)
							{
								if(!$rsubmenu)
								{
									$rsubmenu = false; 
								}
										
								echo "			<li><a href='".$link."'>".$page->post_title."</a></li>";
							}
							if(!$menuitems[$rcount + 1]->menu_item_parent && $rsubmenu)
							{
								echo " 		</ul>";
														
								$rsubmenu = false; 
							}
							if(!$menuitems[$rcount + 1]->menu_item_parent)
							{
								echo "	</div>
									";
											 
								$rsubmenu = false; 
							}
									
							$rcount++; 
						endforeach; 
					?>        
				</div>
          	</div>
		</footer> 
		<?php
	}
}	
// register widget
add_action('widgets_init', create_function('', 'return register_widget("footer");'));
?>