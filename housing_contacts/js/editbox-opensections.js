function openBuildings(building_name)
{
	if(building_name=='one')
	{
		$('#building-type_multi').css('display','none');
		$('#building-type_custom').css('display','none');
		$('#building-type_one').css('display','inherit');
	}
	else if(building_name=='multi')
	{
		$('#building-type_one').css('display','none');
		$('#building-type_custom').css('display','none');
		$('#building-type_multi').css('display','inherit');
	}
	else if(building_name=='custom')
	{
		$('#building-type_one').css('display','none');
		$('#building-type_multi').css('display','none');
		$('#building-type_custom').css('display','inherit');
	}
}
			
function openDateType(date_type)
{
	if(date_type=='date')
	{
		$('#date-type_datespan').css('display','none');
		$('#date-type_date').css('display','inherit');
	}
	else if(date_type=='datespan')
	{
		$('#date-type_date').css('display','none');
		$('#date-type_datespan').css('display','inherit');
	}
}
			
function openEvent(event_name)
{
	if(event_name=='general')
	{
		$('#event-move').css('display','none');
	}
	else if(event_name=='move')
	{
		$('#event-move').css('display','inherit');
	}
}
			
function openLots(lot_name)
{
	if(lot_name=='one')
	{
		$('#lot-type_multi').css('display','none');
		$('#lot-type_custom').css('display','none');
		$('#lot-type_one').css('display','inherit');
	}
	else if(lot_name=='multi')
	{
		$('#lot-type_one').css('display','none');
		$('#lot-type_custom').css('display','none');
		$('#lot-type_multi').css('display','inherit');
	}
	else if(lot_name=='custom')
	{
		$('#lot-type_one').css('display','none');
		$('#lot-type_multi').css('display','none');
		$('#lot-type_custom').css('display','inherit');
	}
}
			
function openMoveBreakdown(breakdown_name)
{
	if(breakdown_name=='floor')
	{
		$('#movebreakdown-type_floor').css('display','inherit');
	}
	else if(breakdown_name=='building')
	{
		$('#movebreakdown-type_floor').css('display','none');
	}
}