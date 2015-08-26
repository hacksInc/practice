$(function(){

	$('.header_menu_list, .header_menu_list_detail').hover(hover_menu);

    $(document).on('click', '.keep_data', keep_check);

	$(document).on('click', '.keep_delete', keep_delete);

	function hover_menu() {
		$(this).find('.header_menu_list_detail').stop().slideToggle();
	}

	function menu_toggle(event) {
		event.preventDefault();
		$('.header_menu').slideToggle();
	}
	
	function keep_check() {

		var project_id = $(this).attr('value');
        $.ajax({
            url: "/keeps/add",
            type: "POST",
            dataType: "json",
            data: { name : project_id },
            success : function(data){            
            	var button = $('.keep_data[value='+project_id+']');
            	button.addClass('keep_delete').removeClass('keep_data');
            	$('.keep_count').text('('+data.keep_count+'件)');
	        },
        });
	}

	function keep_delete(){

		var project_id = $(this).attr('value');
		 $.ajax({
            url: "/keeps/delete",
            type: "POST",
            dataType: "json",
            data: { name : project_id },
            success : function(data){
            	var button = $('.keep_delete[value='+project_id+']');
            	button.addClass('keep_data').removeClass('keep_delete');
            	$('.keep_count').text('('+data.keep_count+'件)');
	        },
        });
	}
});



