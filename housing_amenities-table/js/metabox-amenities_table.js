var tabs = ['building_features', 'room_amenities', 'provided_students', 'table_caption'];

jQuery(document).ready(function()
{
	jQuery('a.amenity-tab').click(function(event)
	{
		event.preventDefault();
		
		for(x = 0; x < tabs.length; x++)
		{
			if(tabs[x]==jQuery(this).attr('data-tab'))
			{
				if(jQuery(this).hasClass('selected')==false)
				{
					jQuery(this).addClass('selected');
					jQuery('#' + tabs[x]).css('display', 'inherit');
				}
			}
			else
			{
				if(jQuery('a.amenity-tab[data-tab="'+tabs[x]+'"]').hasClass('selected'))
				{
					jQuery('a.amenity-tab[data-tab="'+tabs[x]+'"]').removeClass('selected');
					jQuery('#' + tabs[x]).css('display', 'none');
				}
			}
		}
	});
});



/*
function openAmenitiesTab(tab)
{
	var buildingfeatures 	= jQuery('#building_features'),
		roomamenities		= jQuery('#room_amenities'), 
		providedstudents	= jQuery('#provided_students'),
		tablecaption		= jQuery('#table_caption');
					
	if(tab=='buildingfeatures')
	{	
		roomamenities.css('display', 'none');
		providedstudents.css('display', 'none');
		tablecaption.css('display', 'none');
					
		buildingfeatures.css('display', 'inherit');
	}
	else if(tab=='roomamenities')
	{
		buildingfeatures.css('display', 'none');
		providedstudents.css('display', 'none');
		tablecaption.css('display', 'none');
					
		roomamenities.css('display', 'inherit');
	}
	else if(tab=='providedstudents')
	{
		buildingfeatures.css('display', 'none');
		roomamenities.css('display', 'none');
		tablecaption.css('display', 'none');
					
		providedstudents.css('display', 'inherit');
	}
	else if(tab=='tablecaption')
	{
		buildingfeatures.css('display', 'none');
		roomamenities.css('display', 'none');
		providedstudents.css('display', 'none');
					
		tablecaption.css('display', 'inherit');
	}
}
*/

function showWifi() 
{ 
	jQuery('#wifi-hidden').css('display', 'inherit'); 
}
function hideWifi() 
{ 
	jQuery('#wifi-hidden').css('display', 'none'); 
}