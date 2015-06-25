$(window).load(function(){
	'use strict';
var waiteForSetTheme = setInterval(function() {
  
        if($("body")[0].className.split(' ')[0] != ''){
  
        clearInterval(waiteForSetTheme);
        setWindowSize();	
        setWindowSize2();	
	var displayTime = 1000;
	var documentW = $(document).width();
	var documentH = $(document).height();
	var copy = $('img#copy');
	var copyW = copy.width();
	var copyH = copy.height();
	var opening = $('div#opening');
	var character = $('img#character');
	var characterName = $("body")[0].className.split(' ')[0];
	var characterW = character.width();
	var characterH = character.height();
	var characterStartPosR = characterW;
	var characterOutW = 80;
	var characterR = -characterOutW;
	var characterRAdjust = documentW - characterW;
	var characterBlurSpace = 200;
	var characterBlurStartPosR1 = characterStartPosR + characterBlurSpace;
	var characterBlurStartPosR2 = characterStartPosR + characterBlurSpace * 2;
	var animationDuration = 1500;
	var copyDuration = 5000;
	
	if ( characterRAdjust < characterOutW ) {
		if ( characterRAdjust <= 0 ) {
			characterR = -characterRAdjust + characterR;
		}
	}
	
	if ( characterName == 'ginoza' ) {
		characterR = characterOutW;
		characterStartPosR = -documentW;
		characterBlurStartPosR1 = characterStartPosR - characterBlurSpace;
		characterBlurStartPosR2 = characterStartPosR - characterBlurSpace * 2;
		
		copy.css({
			'left': 'initial',
			'right': 10,
			'top': documentH - characterH + copyH / 2,
			'bottom': 'initial'
		});
	}
	
	if ( characterName == 'masaoka' ) {
		copy.css({
			'left': 5,
			'top': documentH - characterH + copyH / 2,
			'bottom': 'initial'
		});
	}
	
	if ( characterName == 'karanomori' ) {
		characterR = 0;
		
		copy.css({
			'left': 'initial',
			'right': 10,
			'top': documentH - characterH + copyH / 2,
			'bottom': 'initial'
		});
	}
	
	if ( characterName == 'kunizuka' ) {
		characterR = characterOutW;
		characterStartPosR = -documentW;
		characterBlurStartPosR1 = characterStartPosR - characterBlurSpace;
		characterBlurStartPosR2 = characterStartPosR - characterBlurSpace * 2;
		
		copy.css({
			'left': 'initial',
			'right': 10
		});
	}
	
	copy.css({
		'width': copyW / 2,
		'height': copyH / 2,
		'opacity': 0
	});
	
	opening.css({
		'padding-top': documentH - characterH,
		'right': characterStartPosR,
		'zIndex': 3,
		'opacity': 1
	}).animate(
		{ right: characterR },
		{
			duration: animationDuration,
			easing: 'easeInOutSine',
			complete: function() {
				copy.animate ({ opacity: 100 }, { duration: copyDuration, easing: 'easeInQuad', complete: function() {
					$(this).delay(displayTime).queue(function() {
						$(location).attr( 'href', 'index.html' );
					});
				}});
			}
		}
	);
	
	opening.clone().insertAfter(opening).css({
		'right': characterBlurStartPosR1,
		'opacity': '0.6',
		'zIndex': 2
	}).animate(
		{ right: characterR },
		{
			duration: animationDuration,
			easing: 'easeInOutSine',
			complete: function() {
				$(this).remove();
			}
		}
	);
	
	opening.clone().insertAfter(opening).css({
		'right': characterBlurStartPosR2,
		'opacity': '0.3',
		'zIndex': 1
	}).animate(
		{ right: characterR },
		{
			duration: animationDuration,
			easing: 'easeInOutSine',
			complete: function() {
				$(this).remove();
			}
		}
	);
     }
},1000);
});
