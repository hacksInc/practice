
$(function(){

    var sd = $( "input#php_server_domain" ).val();

    $(document)

        .on('click', '#vote_btn', function(){

            var obj = new String($('select[name="name1"] option:selected').val());
            var name = obj.split(",");
            var name1 = name[0].replace("'","");
            var name2 = name[1].replace("'","");
            name1 = name1.replace("'","");
            name2 = name2.replace("'","");

            $('#name1').text(name1);
            $('#name2').text(name2);

            var point = $('select[name="usePoint"] option:selected').val();
            var myPt = $('#myPt').text();
            var afterPt = +myPt - +point;

            $('#popup_confirm').show();
            $('#overlay').show();

            $('.point span').text(point);
            $('#beforePt').text(myPt);

            if (afterPt > 0) {
                $('#afterPt').text(afterPt);
            }

            return false;

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

            var item_id = $('#name1').text();
            var item_id2 = $('#name2').text();
            var point = $('select[name="usePoint"] option:selected').val();
            
            Unity.call( "https://" + sd + "/psychopass_portal/execvoting.php?item_id=" + item_id + "&item_id2=" + item_id2 + "&point=" + point );
            return false;
        });


});
