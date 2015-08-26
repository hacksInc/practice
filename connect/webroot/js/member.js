
$(function(){


	$(document).on('click', '.keep_delete', entry_delete);


	function entry_delete(){
		
		$(this).closest('section').remove();

		if( $('.keep_project').children('section').length <= 0 ) {
			$('.keep_project').remove();
		}
	}
});



