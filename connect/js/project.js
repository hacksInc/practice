$(function(){

	if(!navigator.userAgent.match(/(iPhone|iPad|Android)/)){

		$('.header_menu_list, .header_menu_list_detail').hover(hover_menu);

	} else {

		var headerH = $('.header').height();
		window.onload = function(){
			$('.main').css({'padding-top': headerH});
		}
		$(document).on('touchstart', '.header_menu_toggle', menu_toggle);

	}
	
	function hover_menu() {
		$(this).find('.header_menu_list_detail').stop().slideToggle();
	}

	function menu_toggle(event) {
		event.preventDefault();
		$('.header_menu').slideToggle();
	}
	
});
