$(function() {
	'use strict';
	
	var sd = $( "input#php_server_domain" ).val();
	
//	$("#form-send").unbind().submit(function(){
	$("#input-submit").unbind().click(function(){
		var textValue = $("#input-text").val();
		
		Unity.call( "https://" + sd + "/psychopass_portal/serialRegist.php?sc=" + textValue );
	});
	
//	$("#input-submit").unbind().click(function(){
	$(window).keydown(function(e){
		if ( e.keyCode == 13 ) {
			
			var textValue = $("#input-text").val();
			
			Unity.call( "https://" + sd + "/psychopass_portal/serialRegist.php?sc=" + textValue );
		}
	});
});
