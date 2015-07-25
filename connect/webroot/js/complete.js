$(function(){
	
	var headerH = $('.header').height();
	window.onload = function(){
		$('.main').css({'padding-top': headerH});
	}

	setTimeout(function(){
		location.href = 'http://192.168.33.10/';
	},5000);
});