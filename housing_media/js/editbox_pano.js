// objects
var panoramas = []; // panoramas

// reference - do not change!! - dev note: can be changed, most js code should scale (additional changes may be needed [HTML])	
var max_panoramas = 4; 	
	
// selectors
var selector_numpanos_input = jQuery('select[name="num_panoramas"]'),
	selector_panoramas_container = jQuery('.media-container.pano'),
	selector_panoramas_tab = 'a.media-tab.pano',
	selector_panoramas_tabscontainer = jQuery('#panorama-tab-container');
	
// HTML elements
var HTML_panoramas_tab = function(tab_num){return '<a class="media-tab pano" href="#" data-tab="'+ tab_num +'"><mobile>'+ tab_num +'</mobile><desktop>Panorama '+ tab_num +'</desktop></a>'};


// panorama object
function Panorama(index)
{
	// reference
	this.id = index;
	
	// settings
	this.isDisplayed = false;
	
	// jQuery selectors
	this.selector = jQuery('#panorama-'+this.id);
	
	// simple functions
	this.display = function(display)
	{
		// hide or show this panorama and set the 'isDisplayed' property
		if(display=='show'){jQuery(this.selector).css('display', 'inherit'); this.isDisplayed = true;} 
		else if(display=='hide'){jQuery(this.selector).css('display', 'none'); this.isDisplayed = false;}
	}
}
for(x = 1; x <= max_panoramas; x++){panoramas[x] = new Panorama(x);} // create panoramas

function initialize()
{
	selectPanoramas(selector_numpanos_input.val());
}
initialize();

// input functions 
function selectPanoramas(num_panoramas)
{
	if(num_panoramas != 'Number of panoramas')
	{
		selector_panoramas_tabscontainer.html(''); for(x = 1; x <= num_panoramas; x++){selector_panoramas_tabscontainer.append(HTML_panoramas_tab(x));}
		reloadPanoramaTabs();
		selector_panoramas_tabscontainer.css('display', 'block');
		
		for(x = 1; x <= max_panoramas; x++){if(panoramas[x].isDisplayed){panoramas[x].display('hide');}}
		panoramas[1].display('show');
		selector_panoramas_container.css('display', 'block');
	}
	else
	{
		selector_panoramas_tabscontainer.css('display', 'none').html('');
		selector_panoramas_container.css('display', 'none');
		for(x = 1; x <= max_panoramas; x++){if(panoramas[x].isDisplayed){panoramas[x].display('hide'); panoramas[x].color();}}
	}
}

function reloadPanoramaTabs()
{
	var first_tab = jQuery(selector_panoramas_tab)[0];
	if(!jQuery(first_tab).hasClass('selected')){jQuery(first_tab).addClass('selected')};
	
	var panorama_tabs = [], x = 1; jQuery(selector_panoramas_tab).each(function(){panorama_tabs[x] = this; x++;});
	
	jQuery(selector_panoramas_tab).click(function(e)
	{
		e.preventDefault();
		
		for(x = 1; x < panorama_tabs.length; x++)
		{
			if(x==jQuery(this).attr('data-tab')){if(jQuery(this).hasClass('selected')==false){jQuery(this).addClass('selected'); panoramas[x].display('show');}}
			else if(jQuery(selector_panoramas_tab + '[data-tab="'+x+'"]').hasClass('selected')){jQuery(selector_panoramas_tab + '[data-tab="'+x+'"]').removeClass('selected'); panoramas[x].display('hide');}
		}
	});
}

function revealHTML5Options(panoID, yes_or_no){
	if(yes_or_no=='Yes'){
		jQuery('#panorama-'+panoID+'-html5-options').show();	
	}
	else if(yes_or_no=='No'){
		jQuery('#panorama-'+panoID+'-html5-options').hide();
	}
}

function replaceExistingPanoImage(fileDivID, inputDivID){
	jQuery('#'+fileDivID).hide();	
	jQuery('#'+inputDivID).show();	
}