

$(function(){
	var sd = $( "input#php_server_domain" ).val();
	
    $(document)
        .on('click', '#vote_btn', function(){

            var mypt = $('#Pt span').text();
            var name = $('select[name="name"] option:selected').text();
            var item_id = $('select[name="name"]').val();
            var point = $('select[name="point"]').val();

            if ( +mypt < +5 ) {
                
                $('#popup_alert').show();
                $('#overlay').show();
                return false;

            } else if( +mypt >= +point ) {

                $('#popup_confirm').show();
                $('#overlay').show();
                $('.name').text(name);
                $('.point span').text(point);

                return false;

            } else {
                
                $('#popup_alert').show();
                $('#overlay').show();
                return false;
            }


        })

        .on('click', '#ranking_btn, #result_rank_btn', function(){
			$('#popup_ranking').show();
	        $('#overlay').show();
	           return false;
        })
        
        .on('click', '.cancel, #overlay', function(){
            $('#popup_confirm, #overlay, #popup_ranking, #popup_alert').hide();
            return false;
        })
        
        .on('click', '.accept', function(){
        	var item_id = $('select[name="name"]').val();
            var point = $('select[name="point"]').val();
            
            Unity.call( "https://" + sd + "/psychopass_portal/execvoting.php?item_id=" + item_id + "&point=" + point );
            return false;
        });
});