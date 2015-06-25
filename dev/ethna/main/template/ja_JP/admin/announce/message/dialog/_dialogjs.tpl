{literal}
<script>
    $("#btnLineBreak").click(function (){
        var message_obj = $("#dialogMessage").get(0),
            message_string = $("#dialogMessage").val(),
            before_string = '',
            after_string = '',
            message = '',
            pos = 0;

        pos = message_obj.selectionStart;
        before_string = message_string.substr(0, pos);
        after_string = message_string.substr(pos);

        message = before_string + '<BR>' + after_string;
        $("#dialogMessage").val(message);
    });

    $(".message-string-palette").click( function(){
        var message_obj = $("#dialogMessage").get(0),
            message_string = $("#dialogMessage").val(),
            before_string = '',
            after_string = '',
            message = '',
            pos = 0,
            color = '',
            color_string = '';

        color = $(this).css("background-color");
        switch (color){
        case "#000000":
        case "rgb(0, 0, 0)":
            color_string = "[000000]"
            break;
        case "#FFCC00":
        case "rgb(255, 204, 0)":
            color_string = "[FFCC00]"
            break;
        case "#FFFF00":
        case "rgb(255, 255, 0)":
            color_string = "[FFFF00]"
            break;
        case "#008000":
        case "rgb(0, 128, 0)":
            color_string = "[008000]"
            break;
        case "#FF0000":
        case "rgb(255, 0, 0)":
            color_string = "[FF0000]"
            break;
        case "#99CC00":
        case "rgb(153, 204, 0)":
            color_string = "[99CC00]"
            break;
        case "#0000FF":
        case "rgb(0, 0, 255)":
            color_string = "[0000FF]"
            break;
        case "#FF00FF":
        case "rgb(255, 0, 255)":
            color_string = "[FF00FF]"
            break;
        case "#FFFFFF":
        case "rgb(255, 255, 255)":
            color_string = "[FFFFFF]"
            break;
        }
        pos = message_obj.selectionStart;
        before_string = message_string.substr(0, pos);
        after_string = message_string.substr(pos);

        message = before_string + color_string + after_string;
        $("#dialogMessage").val(message);

    });
</script>	
{/literal}
