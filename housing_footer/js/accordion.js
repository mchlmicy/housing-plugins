jQuery(document).ready(function() 
{
	//Remove #'s from URLs on anchor clicks
	jQuery('a.accordion-toggle').click(function(event){event.preventDefault(); accordionHighlight(this);});
});

jQuery(window).resize(function()
{
	if(jQuery(window).width()>=600)
	{
		jQuery('#footer-sitemap_responsive .mobile-sitemap_title-container a.accordion-toggle').each(function()
		{
			if(jQuery(this).hasClass('active'))
			{
				jQuery(this).removeClass('active');
			}
		});
		
		jQuery('#footer-sitemap_responsive .footer-dropdown').each(function() 
		{
			if(jQuery(this).hasClass('expand'))
			{
				jQuery(this).removeClass('expand').addClass('collapse');
			}
		});
	}
});

function accordionHighlight(tab)
{	
	if(jQuery(tab).hasClass('active'))
	{
		jQuery(tab).removeClass('active');
	}
	else
	{
		jQuery('#footer-sitemap_responsive .mobile-sitemap_title-container a.accordion-toggle').each(function()
		{
			if(jQuery(this).hasClass('active'))
			{
				jQuery(this).removeClass('active');
			}
		});
	
		jQuery(tab).addClass('active');
	}
}

function toggleAccordion(accordionID)
{
	jQuery('#footer-sitemap_responsive .footer-dropdown').each(function() 
	{
  		if(!jQuery(this).is('#footerdropdown-' + accordionID))
		{
			if(jQuery(this).hasClass('expand'))
			{
				jQuery(this).removeClass('expand').addClass('collapse');
			}
		}
	});
	
	if(jQuery('#footerdropdown-' + accordionID).hasClass('collapse'))
	{
		jQuery('#footerdropdown-' + accordionID).removeClass('collapse');
		jQuery('#footerdropdown-' + accordionID).addClass('expand');
	}
	else if(jQuery('#footerdropdown-' + accordionID).hasClass('expand'))
	{
		jQuery('#footerdropdown-' + accordionID).removeClass('expand');
		jQuery('#footerdropdown-' + accordionID).addClass('collapse');
	}
}