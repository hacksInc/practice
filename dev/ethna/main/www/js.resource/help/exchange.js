
function exchange(){
	var pt = $('select[name="exchange_num"] option:selected').val();
	$('#confirmText span').text(pt);
}
function result(){
	var sd = location.host;
	Unity.call('https://' + sd + '/psychopass_game/resource/point/exchangeResult');
	return false;
}

