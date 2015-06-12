

$(function(){
	$('.list:odd').find(".frame1").addClass("green1");
	$('.list:odd').find(".frame2").addClass("green2");

});

function back(){
	var ref = document.referrer;
	location.href = ref;
	return false;
}