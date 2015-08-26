$(function(){
	
	var headerH = $('.header').height();
	window.onload = function(){
		$('.main').css({'padding-top': headerH});
	}

	setTimeout(function(){
		location.href = 'http://dev.connect-job.com/';
	},5000);
});