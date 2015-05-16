jQuery('a.arrow').click(function(event)
{
	event.preventDefault();
	var num_panos = 0, parent = jQuery(this).parent().parent();
	
	jQuery('.panorama-module .thumbnail-container').each(function()
	{
		jQuery(this).css('display', 'none');
		num_panos++;
	});
	
	if(jQuery(parent).data('panonum') != num_panos){jQuery('.panorama-module .thumbnail-container:nth-child('+ (parseInt(jQuery(parent).data('panonum')) + 1) +')').css('display', 'inherit');}
	else{jQuery('.panorama-module .thumbnail-container:nth-child(1)').css('display', 'inherit');}
});
