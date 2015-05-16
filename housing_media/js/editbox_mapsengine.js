// objects
var maps = []; // maps

// reference - do not change!! - dev note: can be changed, most js code should scale (additional changes may be needed [HTML])	
var max_maps = 4; 

// selectors
var selector_nummaps_input = jQuery('select[name="num_maps"]'),
	selector_maps_container = jQuery('.maps-container'),
	selector_maps_tab = 'a.media-tab.map',
	selector_maps_tabscontainer = jQuery('#maps-tab-container');
	
// HTML elements
var HTML_maps_tab = function(tab_num){return '<a class="media-tab map" href="#" data-tab="'+ tab_num +'"><mobile>'+ tab_num +'</mobile><desktop>Map '+ tab_num +'</desktop></a>'};

// map object
function Map(index)
{
	// reference
	this.id = index;
	
	// settings
	this.isDisplayed = false;
	
	// jQuery selectors
	this.selector = jQuery('#map-'+this.id);
	
	// simple functions
	this.display = function(display)
	{
		// hide or show this panel and set the 'isDisplayed' property
		if(display=='show'){jQuery(this.selector).css('display', 'inherit'); this.isDisplayed = true;} 
		else if(display=='hide')
		{
			jQuery(this.selector).css('display', 'none'); this.isDisplayed = false;
		}
	}
}
for(x = 1; x <= max_maps; x++){maps[x] = new Map(x);} // create maps

function initialize()
{
	selectMaps(selector_nummaps_input.val());
}
initialize();

function selectMaps(num_maps)
{
	if(num_maps != 'Number of maps')
	{
		selector_maps_tabscontainer.html(''); for(x = 1; x <= num_maps; x++){selector_maps_tabscontainer.append(HTML_maps_tab(x));}
		reloadMapTabs();
		
		selector_maps_tabscontainer.css('display', 'block');
		
		for(x = 1; x <= max_maps; x++){if(maps[x].isDisplayed){maps[x].display('hide');}}
		maps[1].display('show');
		selector_maps_container.css('display', 'block');
	}
	else
	{
		selector_maps_tabscontainer.css('display', 'none').html('');
		selector_maps_container.css('display', 'none');
		for(x = 1; x <= max_maps; x++){if(maps[x].isDisplayed){maps[x].display('hide');}}
	}
}
function reloadMapTabs()
{
	var first_tab = jQuery(selector_maps_tab)[0];
	if(!jQuery(first_tab).hasClass('selected')){jQuery(first_tab).addClass('selected')};
	
	var map_tabs = [], x = 1; jQuery(selector_maps_tab).each(function(){map_tabs[x] = this; x++;});
	
	jQuery(selector_maps_tab).click(function(e)
	{
		e.preventDefault();
		
		for(x = 1; x < map_tabs.length; x++)
		{
			if(x==jQuery(this).attr('data-tab')){if(jQuery(this).hasClass('selected')==false){jQuery(this).addClass('selected'); maps[x].display('show');}}
			else if(jQuery(selector_maps_tab + '[data-tab="'+x+'"]').hasClass('selected')){jQuery(selector_maps_tab + '[data-tab="'+x+'"]').removeClass('selected'); maps[x].selector.css('display','none');}
		}
	});
}