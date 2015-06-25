function exchange(){

	var pt = $('select[name="changePt"] option:selected').val();
	$('#confirmPt span').text(pt);
	$(".scroll").niceScroll();
	return false;
}
