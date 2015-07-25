
$(function(){

	if(!navigator.userAgent.match(/(iPhone|iPad|Android)/)){

		$(document).on('click', '.keep_delete', keep_delete);

	} else {

		var headerH = $('.header').height();
		window.onload = function(){
			$('.main').css({'padding-top': headerH});
		}

		$(document).on('touchstart', '.keep_delete', keep_delete);

	}

	function keep_delete(){

		var project_id = $(this).attr('value');
		 $.ajax({
            url: "/keeps/delete",
            type: "POST",
            dataType: "json",
            data: { name : project_id },
            success : function(data){
                if ( data.keep_count == 0 ) {
                    $('.keep_project').remove();
                } else {
                    var button = $('.keep_delete[value='+project_id+']');
                    button.closest('section').remove();
                }
	        },
        });
	}
});



