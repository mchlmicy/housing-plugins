function openBuildings(building_name)
{
	if(building_name=='one')
	{
		jQuery('#building-type_multi').css('display','none');
		jQuery('#building-type_custom').css('display','none');
		jQuery('#building-type_one').css('display','inherit');
	}
	else if(building_name=='multi')
	{
		jQuery('#building-type_one').css('display','none');
		jQuery('#building-type_custom').css('display','none');
		jQuery('#building-type_multi').css('display','inherit');
	}
	else if(building_name=='custom')
	{
		jQuery('#building-type_one').css('display','none');
		jQuery('#building-type_multi').css('display','none');
		jQuery('#building-type_custom').css('display','inherit');
	}
}
			
function openDateType(date_type)
{
	if(date_type=='date')
	{
		jQuery('#date-type_datespan').css('display','none');
		jQuery('#date-type_date').css('display','inherit');
	}
	else if(date_type=='datespan')
	{
		jQuery('#date-type_date').css('display','none');
		jQuery('#date-type_datespan').css('display','inherit');
	}
}
			
function openEvent(event_name)
{
	if(event_name=='general')
	{
		jQuery('#event-move').css('display','none');
	}
	else if(event_name=='move')
	{
		jQuery('#event-move').css('display','inherit');
	}
}
			
function openLots(lot_name)
{
	if(lot_name=='one')
	{
		jQuery('#lot-type_multi').css('display','none');
		jQuery('#lot-type_custom').css('display','none');
		jQuery('#lot-type_one').css('display','inherit');
	}
	else if(lot_name=='multi')
	{
		jQuery('#lot-type_one').css('display','none');
		jQuery('#lot-type_custom').css('display','none');
		jQuery('#lot-type_multi').css('display','inherit');
	}
	else if(lot_name=='custom')
	{
		jQuery('#lot-type_one').css('display','none');
		jQuery('#lot-type_multi').css('display','none');
		jQuery('#lot-type_custom').css('display','inherit');
	}
}
			
function openMoveBreakdown(breakdown_name)
{
	if(breakdown_name=='floor')
	{
		jQuery('#movebreakdown-type_floor').css('display','inherit');
	}
	else if(breakdown_name=='building')
	{
		jQuery('#movebreakdown-type_floor').css('display','none');
	}
}