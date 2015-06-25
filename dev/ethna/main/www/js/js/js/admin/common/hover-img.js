$(function(){
	$('a.hoverImg').popover({
		html: true,
		trigger: 'hover',
		content: function(){
			return '<img src="' + $(this).attr('href') + '" />';
		}
	});
});
