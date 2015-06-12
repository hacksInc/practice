
    
    function vote() {
        
        var myPt = $('#myPt').text();
        
        if ( myPt == '0' ) {

            $('#popup_alert').show();
            $('.alert span').text('所持票数が足りません。[GAME]にて潜在犯を執行し、票数を確保してください');
            $('#overlay').show();
            return false;

        } else {

            var obj = new String($('select[name="name1"] option:selected').val());
            var name = obj.split(",");
            var name1 = name[0].replace("'","");
            var name2 = name[1].replace("'","");
            name1 = name1.replace("'","");
            name2 = name2.replace("'","");

            var point = $('select[name="usePoint"] option:selected').val();
            var afterPt = +myPt - +point;

            $('#popup_confirm').show();
            $('#overlay').show();

            $('#name1').text(name1);
            $('#name2').text(name2);
            $('.point span').text(point);
            $('#beforePt').text(myPt);

            if ( +afterPt == 0 ) {
                $('#afterPt').text('0');
            } else {
                $('#afterPt').text(afterPt);
            }

            return false;

        }

    }

    function rank() {

        $('#popup_ranking').show();
        $('#overlay').show();
        $(".scroll").niceScroll();

        return false;

    }

    function cancel() {

        $('#popup_confirm, #overlay, #popup_ranking, #popup_alert').hide();
        return false;

    }

    function accept() {

        var sd = $( "input#php_server_domain" ).val();
        var item_id = $('#name1').text();
        var item_id2 = $('#name2').text();
        var point = $('select[name="usePoint"] option:selected').val();
            
        Unity.call( "https://" + sd + "/psychopass_portal/execvoting.php?item_id=" + item_id + "&item_id2=" + item_id2 + "&point=" + point );
        return false;

    }

    function start() {

        var sd = $( "input#php_server_domain" ).val();
        $(".scroll").niceScroll();
        return false;
    }
