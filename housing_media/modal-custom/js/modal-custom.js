// event listenter
function listen_modalCustom()
{
    jQuery('a.modal-custom').on('click', function(event)
	{
		event.preventDefault();
		modalCustom_initialize(this);
	});
}
jQuery(document).ready(function(){listen_modalCustom();});

// modal object
var modal;

// reference

// modal object constructor
function Modal(modalType, vars)
{
    // reference
	this.body,
    this.header,
    this.footer,
    this.type = modalType,
    this.vars; if(vars!=null){this.vars = JSON.parse(vars);}

    // templates
    this.simplicity_template = [],
    this.structure_template =  ['{"name": "lightbox"}',
								'{"name": "mapsengine"}',
								'{"name": "panorama"}',
								'{"name": "videos"}'];

	// settings
    this.simplicity = function(){if(jQuery.inArray(this.type, this.simplicity_template) == -1){return 'complex';} else{return 'simple';}}
	this.isDisplayed = false;

	// HTML elements
	this.HTML_shell = jQuery("<div class='modal fade' id='modalCustom' tabindex='-1' aria-hidden='true'><div class='modal-dialog'><div class='modal-content'></div></div></div>"),
    this.HTML_trigger = "<a href='#modalCustom' id='modalCustomButton' data-toggle='modal'></a>",
    this.HTML_content = function(modalTitle, modalBody, modalFooter){return "<div class='modal-header'><button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>"+ modalTitle +"</div><div class='modal-body'>"+ modalBody +" </div><div class='modal-footer'>"+ modalFooter +"</div>";},
    this.HTML_header = function(modalHeader){return "<h4 class='modal-title'>"+ modalHeader +"</h4>";},
    this.HTML_subheader = function(modalSubheader){return "<h5 class='modal-subtitle' style='margin-top: 0px; margin-bottom: 0px'>"+ modalSubheader +"</h5>"};

    // simple functions
    this.construct = function()
    {
        var template = this.findTemplate();

        if(this.simplicity()=='complex'){jQuery(this.HTML_shell).addClass('complex');}
        if(template.hasOwnProperty('subheader')){this.header = this.HTML_header(template.header) + ' ' + this.HTML_subheader(template.subheader);}else if(template.hasOwnProperty('header')){this.header = this.HTML_header(template.header);}else{this.customHeader();}
        if(template.hasOwnProperty('body')){this.body = template.body;}else{this.customBody();}
        if(template.hasOwnProperty('footer')){this.footer = template.footer;}else{this.customFooter();}

        jQuery(this.HTML_shell).find('.modal-content').html(this.HTML_content(this.header, this.body, this.footer)).append(this.HTML_trigger);
        jQuery(this.HTML_shell).on('shown.bs.modal', function(){if(modal.vars.hasOwnProperty('postload_script')){modal.customScript('post_load');}});
		jQuery(this.HTML_shell).on('hidden.bs.modal', function()
        {
            this.remove();
            if(modal.hasOwnProperty('redirect')){window.location = modal.vars.redirect;}
			modal = null;
		});
    }
    this.findTemplate = function()
    {
        for(x = 0; x < this.structure_template.length; x++){if(JSON.parse(this.structure_template[x]).name==this.type){return JSON.parse(this.structure_template[x]);}}return 'failure';
    }
}// modal methods
Modal.prototype.customHeader = function()
{
   	if(modal.type == 'lightbox'){modal.header = modal.HTML_header(modal.vars.page_title) + ' ' + modal.HTML_subheader();}
	else if(modal.type == 'mapsengine'){modal.header = modal.vars.page_title;}
	else if(modal.type == 'panorama'){modal.header = modal.HTML_header(modal.vars.page_title) + ' ' + modal.HTML_subheader();}
	else if(modal.type == 'videos'){modal.header = modal.vars.page_title;}
	else if(modal.vars.hasOwnProperty('header')){modal.header = modal.HTML_header(modal.vars.header);}
    else
    {
        console.log('a customHeader() function for this modal-type has not been defined or data fields are missing.');
    }
}
Modal.prototype.customBody = function()
{
    if(modal.type == 'lightbox')
	{
		var album_set = []; jQuery('.modal-custom[data-lightbox="'+modal.vars.album+'"]').each(function(){var img_obj = {"img_url": "", "img_desc": "", "img_alt": ""}; img_obj.img_url = jQuery(this).attr('href'); img_obj.img_desc = jQuery(this).attr('title'); img_obj.img_alt = jQuery(this).find('img').attr('alt'); /* preload */ img = new Image(); img.src = img_obj.img_url; album_set.push(img_obj);}); modal.vars.album_set = album_set; modal.vars.index = modal.customScript('findIndex'); 
		modal.body = '<div class="lightbox-container"><div class="lightbox-overlay"><div class="highlight left"><div class="highlight-inner"><a href="#" class="arrow" data-dir="prev">&lsaquo; Prev</a></div></div><div class="highlight right"><div class="highlight-inner"><a href="#" class="arrow" data-dir="next">Next &rsaquo;</a></div></div></div><img class="lightbox-image" src="'+album_set[modal.vars.index].img_url+'" width="100%"/><div class="lightbox-description"><div class="description-inner">'+album_set[modal.vars.index].img_desc+'</div></div></div>'; 
		modal.customScript('listen_lightbox'); modal.customScript('initialize_lightbox');
	}
	else if(modal.type == 'mapsengine'){var map_set = []; jQuery('.modal-custom[data-mapsengine]').each(function(){var map_obj = {"embed_url": "", "map_url": ""}; map_obj.embed_url = jQuery(this).attr('href'); map_obj.map_url = jQuery(this).attr('href').replace('embed','viewer'); map_set.push(map_obj);}); modal.vars.map_set = map_set; modal.vars.index = modal.customScript('findIndex'); modal.body = '<iframe src="'+modal.vars.map_set[modal.vars.index]['embed_url']+'" style="min-height: 350px" width="100%"></iframe>';}
	else if(modal.type == 'panorama')
	{
		var pano_set = []; jQuery('.modal-custom[data-panorama]').each(function(){var pano_obj = {"swf_url": "", "xml_url": "", "pano_alt": "", "format": ""}; pano_obj.swf_url = jQuery(this).attr('href'); pano_obj.xml_url = jQuery(this).data('xml'); pano_obj.pano_alt = jQuery(this).find('img').attr('alt'); pano_set.push(pano_obj);}); modal.vars.pano_set = pano_set; modal.vars.index = modal.customScript('findIndex'); 
		var hasFlash = false; try{var fo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');if(fo) hasFlash = true;} catch(e){if(navigator.mimeTypes ["application/x-shockwave-flash"] != undefined){hasFlash = true;}}
				
		function isCanvasSupported(){var elem = document.createElement('canvas'); return !!(elem.getContext && elem.getContext('2d'));}
				
			 if(hasFlash==true){modal.vars.pano_is_swf = true; modal.body = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="50%"><param name="wmode" value="opaque" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="'+modal.vars.pano_set[modal.vars.index]['swf_url']+'" /><embed src="'+modal.vars.pano_set[modal.vars.index]['swf_url']+'" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="100%" height="320px" wmode="opaque"></embed></object>';} 
		else if (isCanvasSupported()){modal.vars.pano_is_html5 = true; modal.body = '<div id="pano_container" style="width:100%;height:320px;">Loading...</div>'; modal.vars.postload_script = true;}
		
		modal.customScript('listen_panorama'); modal.customScript('initialize_panorama');
	}
    else if(modal.type == 'videos'){var video_set = []; jQuery('.modal-custom[data-videos]').each(function(){var video_obj = {"embed_url": "", "url": ""}; video_obj.url = jQuery(this).attr('href'); if(video_obj.url.indexOf('youtube')>-1){var index = video_obj.url.lastIndexOf('='); video_obj.embed_url = 'https://www.youtube.com/embed/'+video_obj.url.substring(index + 1);}else{video_obj.embed_url = video_obj.url;} video_set.push(video_obj);}); modal.vars.video_set = video_set; modal.vars.index = modal.customScript('findIndex'); modal.body = '<iframe width="100%" height="320" src="'+modal.vars.video_set[modal.vars.index]['embed_url']+'" frameborder="0" allowfullscreen=""></iframe>';}
   	else if(modal.vars.hasOwnProperty('body')){modal.body = modal.vars.body;}
	else
    {
        console.log('a customBody() function for this modal-type has not been defined or data fields are missing.');
    }
}
Modal.prototype.customFooter = function()
{
    if(modal.vars.hasOwnProperty('footer')){modal.footer = modal.vars.footer;}
	else if(modal.type == 'lightbox'){modal.footer = '<a href="#" id="slideshow-button" class="modal-link" data-slideshow="off" style="float: left">slideshow</a><div id="slideshow-count" style="color: #404040; font-weight: 600"><span id="count-index">'+(parseInt(modal.vars.index)+1)+'</span> of <span id="count-total">'+modal.vars.album_set.length+'</span></div>';}
    else if(modal.type == 'mapsengine'){modal.footer = '<a href="'+modal.vars.map_set[modal.vars.index]['map_url']+'" class="modal-link" id="map_link" style="float: left" target="_blank">view full map</a><div id="slideshow-count" style="color: #404040; font-weight: 600"><a href="#" class="modal-link map-toggle" data-dir="prev" style="font-size: 20px; margin-right: 9px">&lsaquo;</a><span id="count-index">'+(parseInt(modal.vars.index)+1)+'</span> of <span id="count-total">'+modal.vars.map_set.length+'</span><a href="#" class="modal-link map-toggle" data-dir="next" style="font-size: 20px; margin-left: 9px">&rsaquo;</a></div>'; modal.customScript('listen_mapsengine');}
    else if(modal.type == 'panorama'){/* <a href="#" id="fullscreen-button" class="modal-link" data-fullscreen="off" style="float: left">fullscreen</a> */modal.footer = '<div id="slideshow-count" style="color: #404040; font-weight: 600"><a href="#" class="modal-link pano-toggle" data-dir="prev" style="font-size: 20px; margin-right: 9px">&lsaquo;</a><span id="count-index">'+(parseInt(modal.vars.index)+1)+'</span> of <span id="count-total">'+modal.vars.pano_set.length+'</span><a href="#" class="modal-link pano-toggle" data-dir="next" style="font-size: 20px; margin-left: 9px">&rsaquo;</a></div>';}
	else if(modal.type == 'videos'){modal.footer = '<a href="'+modal.vars.video_set[modal.vars.index]['url']+'" class="modal-link" id="video_link" style="float: left" target="_blank">view on site</a><div id="slideshow-count" style="color: #404040; font-weight: 600"><a href="#" class="modal-link video-toggle" data-dir="prev" style="font-size: 20px; margin-right: 9px">&lsaquo;</a><span id="count-index">'+(parseInt(modal.vars.index)+1)+'</span> of <span id="count-total">'+modal.vars.video_set.length+'</span><a href="#" class="modal-link video-toggle" data-dir="next" style="font-size: 20px; margin-left: 9px">&rsaquo;</a></div>'; modal.customScript('listen_videos');}
	else
    {
        console.log('a customFooter() function for this modal-type has not been defined or data fields are missing.');
    }
}
Modal.prototype.customScript = function(function_name, vars)
{
    if(modal.type=='lightbox')
	{
			 if(function_name=='findIndex'){for(x = 0; x < modal.vars.album_set.length; x++){if(modal.vars.album_set[x].img_url == modal.vars.initial){return x;}} console.log('lightbox album index not found.');}
		else if(function_name=='initialize_lightbox'){jQuery(modal.HTML_shell).on('show.bs.modal', function(){modal.customScript('update_subheader', '{"subheader": "'+modal.vars.album_set[modal.vars.index].img_alt+'"}');});}
		else if(function_name=='listen_lightbox'){jQuery(modal.HTML_shell).on('shown.bs.modal', function(){jQuery('#modalCustom .lightbox-container .highlight a.arrow').on('click', function(event){event.preventDefault(); var dir = jQuery(this).data('dir'); modal.customScript('toggle_lightbox_image', '{"direction": "'+dir+'"}');}); jQuery('#modalCustom a#slideshow-button').on('click', function(event){event.preventDefault(); modal.customScript('slideshow', '{"status": "'+jQuery('#modalCustom a#slideshow-button').data('slideshow')+'"}');});});}
		else if(function_name=='slideshow'){var json = JSON.parse(vars); if(json.status=='off'){jQuery('#modalCustom a#slideshow-button').data('slideshow', 'on').html('pause'); slideshow = setInterval(function(){modal.customScript('toggle_lightbox_image', '{"direction": "next"}');}, 5000);} else if(json.status=='on'){jQuery('#modalCustom a#slideshow-button').data('slideshow', 'off').html('slideshow'); clearTimeout(slideshow);}}
		else if(function_name=='toggle_lightbox_image'){function validateNewImageIndex(index){if(index < modal.vars.album_set.length && index >= 0){return index;} else if(index < 0){return modal.vars.album_set.length - 1;} else{return 0;}} var json = JSON.parse(vars), next_index; if(json.direction=='next'){next_index = validateNewImageIndex(modal.vars.index + 1);} else if(json.direction=='prev'){next_index = validateNewImageIndex(modal.vars.index - 1);} jQuery('#modalCustom .lightbox-container .lightbox-image').attr('src', modal.vars.album_set[next_index].img_url); modal.vars.index = next_index; modal.customScript('update_subheader', '{"subheader": "'+modal.vars.album_set[modal.vars.index].img_alt+'"}'); modal.customScript('update_description', '{"description": "'+modal.vars.album_set[modal.vars.index].img_desc+'"}'); modal.customScript('update_count', '{"index": "'+modal.vars.index+'"}');}
		else if(function_name=='update_count'){var json = JSON.parse(vars); if(Number.isInteger(parseInt(json.index))){jQuery('#modalCustom #slideshow-count #count-index').html(parseInt(json.index)+1);} else{jQuery('#modalCustom #slideshow-count').remove();}}
		else if(function_name=='update_description'){var json = JSON.parse(vars); if(json.description!=null){jQuery('#modalCustom .modal-body .lightbox-description .description-inner').html(json.description);} else{jQuery('#modalCustom .modal-body .lightbox-description').html('');}}
		else if(function_name=='update_subheader'){var json = JSON.parse(vars); if(json.subheader!=null){jQuery('#modalCustom .modal-header .modal-subtitle').html(json.subheader);} else{jQuery('#modalCustom .modal-header .modal-subtitle').remove();}}
	}
	else if(modal.type=='mapsengine')
	{
			 if(function_name=='findIndex'){for(x = 0; x < modal.vars.map_set.length; x++){if(modal.vars.map_set[x]['embed_url'] == modal.vars.initial){return x;}} console.log('mapengine map set index not found.');}
		else if(function_name=='listen_mapsengine'){jQuery(modal.HTML_shell).on('shown.bs.modal', function(){jQuery('.modal-link.map-toggle').on('click', function(event){event.preventDefault(); modal.customScript('toggle_mapsengine_map', '{"direction": "'+jQuery(this).data('dir')+'"}');});});}
		else if(function_name=='toggle_mapsengine_map'){function validateNewMapIndex(index){if(index < modal.vars.map_set.length && index >= 0){return index;} else if(index < 0){return modal.vars.map_set.length - 1;} else{return 0;}} var json = JSON.parse(vars), next_index; if(json.direction=='next'){next_index = validateNewMapIndex(modal.vars.index + 1);} else if(json.direction=='prev'){next_index = validateNewMapIndex(modal.vars.index - 1);} jQuery('#modalCustom .modal-body iframe').attr('src', modal.vars.map_set[next_index]['embed_url']); modal.vars.index = next_index; modal.customScript('update_count', '{"index": "'+modal.vars.index+'"}'); modal.customScript('update_maplink', '{"map_url": "'+modal.vars.map_set[modal.vars.index]['map_url']+'"}');}
		else if(function_name=='update_count'){var json = JSON.parse(vars); if(Number.isInteger(parseInt(json.index))){jQuery('#modalCustom #slideshow-count #count-index').html(parseInt(json.index)+1);} else{jQuery('#modalCustom #slideshow-count').remove();}}
		else if(function_name=='update_maplink'){var json = JSON.parse(vars); jQuery('#modalCustom .modal-footer #map_link').attr('href', json.map_url);}
	}
	else if(modal.type=='panorama')
	{
			 if(function_name=='findIndex'){for(x = 0; x < modal.vars.pano_set.length; x++){if(modal.vars.pano_set[x]['swf_url'] == modal.vars.initial){return x;}} console.log('panorama set index not found.');}
		else if(function_name=='initialize_panorama'){jQuery(modal.HTML_shell).on('show.bs.modal', function(){modal.customScript('update_subheader', '{"subheader": "'+modal.vars.pano_set[modal.vars.index].pano_alt+'"}');});}
		//else if(function_name=='fullscreen'){console.log('run');  var json = JSON.parse(vars); if(json.status=='off'){jQuery('#modalCustom a#fullscreen-button').data('fullscreen', 'on').html('minimize'); fullscreen = pano.enterFullscreen;} else if(json.status=='on'){jQuery('#modalCustom a#fullscreen-button').data('fullscreen', 'off').html('fullscreen');}}
		else if(function_name=='listen_panorama'){jQuery(modal.HTML_shell).on('shown.bs.modal', function(){jQuery('.modal-link').on('click', function(event){event.preventDefault(); modal.customScript('fullscreen', '{"status": "'+jQuery('#modalCustom a#fullscreen-button').data('fullscreen')+'"}'); modal.customScript('toggle_panorama_pano', '{"direction": "'+jQuery(this).data('dir')+'"}');});});}
		else if(function_name=='post_load')
		{
			//function hideUrlBar(){document.getElementsByTagName("body")[0].style.marginTop="1px";window.scrollTo(0, 1);} 
			
			/*
			window.addEventListener("load", hideUrlBar);
			window.addEventListener("resize", hideUrlBar);
			window.addEventListener("orientationchange", hideUrlBar);
			*/
			
			if(vars){var json = JSON.parse(vars);} 
			
			pano = new pano2vrPlayer("pano_container");
			//hideUrlBar();
			gyro = new pano2vrGyro(pano,"pano_container");
			pano.readConfigUrl(modal.vars.pano_set[modal.vars.index]['xml_url']); 
		}
		else if(function_name=='toggle_panorama_pano')
		{
			function validateNewPanoIndex(index){if(index < modal.vars.pano_set.length && index >= 0){return index;} else if(index < 0){return modal.vars.pano_set.length - 1;} else{return 0;}} 
			
			var json = JSON.parse(vars), next_index; 
			if(json.direction=='next'){next_index = validateNewPanoIndex(modal.vars.index + 1);} else if(json.direction=='prev'){next_index = validateNewPanoIndex(modal.vars.index - 1);} if(modal.vars.hasOwnProperty('pano_is_swf')){jQuery('#modalCustom .modal-body object param[name="movie"]').attr('value', modal.vars.pano_set[next_index]['swf_url']); jQuery('#modalCustom .modal-body object embed').remove(); jQuery('#modalCustom .modal-body object').append('<embed src="'+modal.vars.pano_set[next_index]['swf_url']+'" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="100%" height="320px" wmode="opaque"></embed>');} else if(modal.vars.hasOwnProperty('pano_is_html5')){modal.customScript('post_load', '{"xml_url": "'+modal.vars.pano_set[next_index].xml_url+'"}');} modal.vars.index = next_index; modal.customScript('update_subheader', '{"subheader": "'+modal.vars.pano_set[modal.vars.index].pano_alt+'"}'); modal.customScript('update_count', '{"index": "'+modal.vars.index+'"}');}
		else if(function_name=='update_count'){var json = JSON.parse(vars); if(Number.isInteger(parseInt(json.index))){jQuery('#modalCustom #slideshow-count #count-index').html(parseInt(json.index)+1);} else{jQuery('#modalCustom #slideshow-count').remove();}}
		else if(function_name=='update_subheader'){var json = JSON.parse(vars); if(json.subheader!=null){jQuery('#modalCustom .modal-header .modal-subtitle').html(json.subheader);} else{jQuery('#modalCustom .modal-header .modal-subtitle').remove();}}
	}
	else if(modal.type=='videos')
	{
			 if(function_name=='findIndex'){for(x = 0; x < modal.vars.video_set.length; x++){if(modal.vars.video_set[x]['url'] == modal.vars.initial){return x;}} console.log('video set index not found.');}
		else if(function_name=='listen_videos'){jQuery(modal.HTML_shell).on('shown.bs.modal', function(){jQuery('.modal-link.video-toggle').on('click', function(event){event.preventDefault(); modal.customScript('toggle_video', '{"direction": "'+jQuery(this).data('dir')+'"}');});});}
		else if(function_name=='toggle_video'){function validateNewVideoIndex(index){if(index < modal.vars.video_set.length && index >= 0){return index;} else if(index < 0){return modal.vars.video_set.length - 1;} else{return 0;}} var json = JSON.parse(vars), next_index; if(json.direction=='next'){next_index = validateNewVideoIndex(modal.vars.index + 1);} else if(json.direction=='prev'){next_index = validateNewVideoIndex(modal.vars.index - 1);} jQuery('#modalCustom .modal-body iframe').attr('src', modal.vars.video_set[next_index]['embed_url']); modal.vars.index = next_index; modal.customScript('update_count', '{"index": "'+modal.vars.index+'"}'); modal.customScript('update_videolink', '{"video_url": "'+modal.vars.video_set[modal.vars.index]['video_url']+'"}');}
		else if(function_name=='update_count'){var json = JSON.parse(vars); if(Number.isInteger(parseInt(json.index))){jQuery('#modalCustom #slideshow-count #count-index').html(parseInt(json.index)+1);} else{jQuery('#modalCustom #slideshow-count').remove();}}
		else if(function_name=='update_videolink'){var json = JSON.parse(vars); jQuery('#modalCustom .modal-footer #video_link').attr('href', modal.vars.video_set[modal.vars.index].url);}
	}
	else if(modal.vars.hasOwnProperty('script')){modal.script = modal.vars.script;}
	else
    {
        console.log('a customScript() function for this modal-type has not been defined or dataFields are missing.');
    }
}

// initialize modal function
function modalCustom_initialize(modal_link)
{
	// if this is a lightbox modal
	if(jQuery(modal_link).attr('data-lightbox')){modalCustom('lightbox', '{"album": "'+jQuery(modal_link).data('lightbox')+'", "initial": "'+jQuery(modal_link).attr('href')+'", "page_title": "'+jQuery(modal_link).data('pagetitle')+'"}');}
	else if(jQuery(modal_link).attr('data-mapsengine')){modalCustom('mapsengine', '{"initial": "'+jQuery(modal_link).attr('href')+'", "page_title": "'+jQuery(modal_link).data('pagetitle')+'"}');}
	else if(jQuery(modal_link).attr('data-panorama')){modalCustom('panorama', '{"initial": "'+jQuery(modal_link).attr('href')+'", "page_title": "'+jQuery(modal_link).data('pagetitle')+'"}');}
	else if(jQuery(modal_link).attr('data-videos')){modalCustom('videos', '{"initial": "'+jQuery(modal_link).attr('href')+'", "page_title": "'+jQuery(modal_link).data('pagetitle')+'"}');}
	else
	{
		console.log('this modal type is not currently supported.');
	}  
}

// create modal function
function modalCustom(modalType, vars)
{	
	// check if modal is already displayed
    if(modal == null)
    {
        // create & display modal
        modal = new Modal(modalType, vars)
        modal.construct();

		jQuery(modal.HTML_shell).appendTo('body');
		jQuery('#modalCustom').modal('show');
		modal.isDisplayed = true;	
	}
    else
    {
        // display nothing
    }
}