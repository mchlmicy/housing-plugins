// objects
var videos = []; // videos

// reference - do not change!! - dev note: can be changed, most js code should scale (additional changes may be needed [HTML])	
var max_videos = 4; 

// selectors
var selector_numvideos_input = jQuery('select[name="num_videos"]'),
	selector_videos_container = jQuery('.media-container.videos'),
	selector_videos_tab = 'a.media-tab.videos',
	selector_videos_tabscontainer = jQuery('.media-tab-container.videos');
	
// HTML elements
var HTML_videos_tab = function(tab_num){return '<a class="media-tab videos" href="#" data-tab="'+ tab_num +'"><mobile>'+ tab_num +'</mobile><desktop>Video '+ tab_num +'</desktop></a>'};

// video object
function Video(index)
{
	// reference
	this.id = index;
	
	// settings
	this.isDisplayed = false;
	
	// jQuery selectors
	this.selector = jQuery('#video-'+this.id);
	
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
for(x = 1; x <= max_videos; x++){videos[x] = new Video(x);} // create videos

function initialize()
{
	selectVideos(selector_numvideos_input.val());
}
initialize();

function selectVideos(num_videos)
{
	if(num_videos != 'Number of videos')
	{
		selector_videos_tabscontainer.html(''); for(x = 1; x <= num_videos; x++){selector_videos_tabscontainer.append(HTML_videos_tab(x));}
		reloadVideoTabs();
		
		selector_videos_tabscontainer.css('display', 'block');
		
		for(x = 1; x <= max_videos; x++){if(videos[x].isDisplayed){videos[x].display('hide');}}
		videos[1].display('show');
		selector_videos_container.css('display', 'block');
	}
	else
	{
		selector_videos_tabscontainer.css('display', 'none').html('');
		selector_videos_container.css('display', 'none');
		for(x = 1; x <= max_videos; x++){if(videos[x].isDisplayed){videos[x].display('hide');}}
	}
}
function reloadVideoTabs()
{
	var first_tab = jQuery(selector_videos_tab)[0];
	if(!jQuery(first_tab).hasClass('selected')){jQuery(first_tab).addClass('selected')};
	
	var video_tabs = [], x = 1; jQuery(selector_videos_tab).each(function(){video_tabs[x] = this; x++;});
	
	jQuery(selector_videos_tab).click(function(e)
	{
		e.preventDefault();
		
		for(x = 1; x < video_tabs.length; x++)
		{
			if(x==jQuery(this).attr('data-tab')){if(jQuery(this).hasClass('selected')==false){jQuery(this).addClass('selected'); videos[x].display('show');}}
			else if(jQuery(selector_videos_tab + '[data-tab="'+x+'"]').hasClass('selected')){jQuery(selector_videos_tab + '[data-tab="'+x+'"]').removeClass('selected'); videos[x].selector.css('display','none');}
		}
	});
}