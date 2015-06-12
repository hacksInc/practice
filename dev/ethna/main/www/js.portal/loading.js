$(function() {
	'use strict';
	
	var workWidth = 185;
	var fps = 25;
	var interval = 1 / fps * 1000;
	var frameCount = 1;
	var frameMax = 10;
	var nextFrame = true;
	var workInterval;
	var repeatCount = 1;
	var repeatCountMax = 5;
	
	workInterval = setInterval(intervalEvent, interval);
	
	function intervalEvent() {
		if ( repeatCount <= repeatCountMax ) {
			if ( nextFrame ) {
				if ( frameCount >= frameMax ) {
					frameCount--;
					nextFrame = false;
					repeatCount++;
				} else {
					setPosition();
					frameCount++;
				}
			} else {
				if ( frameCount <= 0 ) {
					nextFrame = true;
				} else {
					setPosition();
					frameCount--;
				}
			}
		} else {
			clearInterval( workInterval );
			$(location).attr( 'href', 'login.html' );
		}
	}
	
	function setPosition() {
		$('div#loading').css({
			"background-position": -workWidth * frameCount + "px" + " 0" 
		});
	}
});
