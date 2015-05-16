// objects
var modules = []; // modules

// reference - do not change!! - dev note: can be changed, most js code should scale (additional changes may be needed [HTML])	
var max_modules = 4; 

// selectors
var selector_nummodules_input = jQuery('select[name="num_modules"]'),
	selector_modules_container = jQuery('.modules-container'),
	selector_modules_tab = 'a.modules-tab',
	selector_modules_tabscontainer = jQuery('#modules-tab-container');
	
// HTML elements
var HTML_modules_tab = function(tab_num){return '<a class="modules-tab" href="#" data-tab="'+ tab_num +'"><mobile>'+ tab_num +'</mobile><desktop>Module '+ tab_num +'</desktop></a>'};

// module object
function Module(index)
{
	// reference
	this.id = index;
	
	// settings
	this.isDisplayed = false;
	
	// jQuery selectors
	this.selector = jQuery('#module-'+this.id);
	
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
for(x = 1; x <= max_modules; x++){modules[x] = new Module(x);} // create modules

function initialize()
{
	selectModules(selector_nummodules_input.val());
}
initialize();

function selectModules(num_modules)
{
	if(num_modules != 'Number of modules')
	{
		selector_modules_tabscontainer.html(''); for(x = 1; x <= num_modules; x++){selector_modules_tabscontainer.append(HTML_modules_tab(x));}
		reloadModuleTabs();
		
		selector_modules_tabscontainer.css('display', 'block');
		
		for(x = 1; x <= max_modules; x++){if(modules[x].isDisplayed){modules[x].display('hide');}}
		modules[1].display('show');
		selector_modules_container.css('display', 'block');
	}
	else
	{
		selector_modules_tabscontainer.css('display', 'none').html('');
		selector_modules_container.css('display', 'none');
		for(x = 1; x <= max_modules; x++){if(modules[x].isDisplayed){modules[x].display('hide');}}
	}
}
function reloadModuleTabs()
{
	var first_tab = jQuery(selector_modules_tab)[0];
	if(!jQuery(first_tab).hasClass('selected')){jQuery(first_tab).addClass('selected')};
	
	var module_tabs = [], x = 1; jQuery(selector_modules_tab).each(function(){module_tabs[x] = this; x++;});
	
	jQuery(selector_modules_tab).click(function(e)
	{
		e.preventDefault();
		
		for(x = 1; x < module_tabs.length; x++)
		{
			if(x==jQuery(this).attr('data-tab')){if(jQuery(this).hasClass('selected')==false){jQuery(this).addClass('selected'); modules[x].display('show');}}
			else if(jQuery(selector_modules_tab + '[data-tab="'+x+'"]').hasClass('selected')){jQuery(selector_modules_tab + '[data-tab="'+x+'"]').removeClass('selected'); modules[x].selector.css('display','none');}
		}
	});
}