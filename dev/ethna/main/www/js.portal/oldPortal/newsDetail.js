$(function() {
	'use strict';
	
	var point_get = parseInt( $("input#php_point_get").val() );
	
	if ( point_get == 1 ) {
		$("body").append('<div id="modal-overlay"></div>');
		$("#modal-overlay").fadeIn("slow");
		
		$("div#modal-content").fadeIn("slow");
		
		//[#modal-overlay]、または[#modal-close]をクリックしたら…
		$("#modal-overlay").unbind().click(function(){

		    //[#modal-content]と[#modal-overlay]をフェードアウトした後に…
		    $("#modal-content,#modal-overlay").fadeOut("slow",function(){
      	 		//[#modal-overlay]を削除する
    	    	$('#modal-overlay').remove();
    	    	$('#modal-content').remove();
	    	});
   		});
	}
});
