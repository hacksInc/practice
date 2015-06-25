$(function() {
	'use strict';
});

$(window).load(function(){
	'use strict';
	
	setWindowSize();
	setWindowSize2();
});

$(window).scroll(function(){
	'use strict';
	
	setWindowSize();
	setWindowSize2();
});

function setTheme( themeName ) {
//function setTheme( 'tsunemori' ) {
	'use strict';
	
	var memberFlg = true;
	
	if ( !themeName ) {
		themeName = 'nonMember';
		memberFlg = false;
	}
	
	var themeStyle = '<link rel="stylesheet" href="css/theme/' + themeName + '.css" media="all">';
	$(themeStyle).appendTo('head');
	
	$('body').addClass(themeName);
	
	if ( !memberFlg ) {
		$('img#character').remove();
	}
	
	$('img.theme').each(function() {
		$(this).attr('src', $(this).attr('src').replace('blank', themeName));
	});
}

function setProfileImg( imgName ) {
	'use strict';
	
	$('img#profileImg').each(function() {
		$(this).attr('src', $(this).attr('src').replace('blank', imgName));
	});
}

function setWindowSize() {
	'use strict';
	
	if ( $('div#window')[0] ) {
		var documentHeight = $(document).height();
		var informationWindow = $('div#window');
		var topOffset = informationWindow.offset();
		var sosialButtonSize = 150;
	
		informationWindow.css("height", documentHeight - topOffset.top - sosialButtonSize);
	}
	
	if ( $('div#controlBox')[0] && ( !$('div.confirm')[0] && !$('div.signupArea')[0] ) ) {
		var controlBox = $('div#controlBox');
		
		controlBox.css({
			"height": controlBox.height(),
			"top": 0,
			"right": 0,
			"bottom": 0,
			"left": 0
		});
	}
}
function setWindowSize2() {
	'use strict';
	
	if ( $('div#window2')[0] ) {
		var documentHeight = $(document).height();
		var informationWindow = $('div#window2');
		var topOffset = informationWindow.offset();
		var sosialButtonSize = 80;
	
		informationWindow.css("height", documentHeight - topOffset.top - sosialButtonSize);
	}
	
	
}
