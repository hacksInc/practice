{literal}
<script>
    /**
    * CSVファイルダウンロード
    * 履歴情報をCSVファイル形式でダウンロードする
    * 
    */
    $("#btnCsvDownload").click( function(){
        var search_param_input=$("#formLogSearch").find("input"),
            search_param_select=$("#formLogSearch").find("select"),
            param_item={},
            input_data='';

        $.each(search_param_input, function(){
            if (this.type!='button'){
                param_item[this.name] = encodeURIComponent($('#'+this.id).val());
            }
        });
        $.each(search_param_select, function(){
            param_item[this.name] = encodeURIComponent($('#'+this.id).val());
        });
        if ($("#searchNameOption").is(':checked') == false) {
            param_item["search_name_option"] = "";
        }
        param_item["search_flg"] = encodeURIComponent('1');
        param_item["ethna_fid"] = "";
        //input_data = JSON.stringify(param_item);

        $.ajax({
            url: "{/literal}{$config.url}{$app.create_file_path}{literal}/createfile",  // リクエストURL
            type: "GET",
            data: param_item, // 送信データ
            dataType: "json", // json
            //cache: true,                                       // キャッシュする
            success: function(data, status, xhr) {               // 通信成功時にデータを表示
                // alert(JSON.stringify(data));
                if (data.code == "200"){
                    $("#downloadFileName").val(data.file_name);
                    $("#formCsvDownload").submit();
                } else if (data.code == "400"){
                    alert(data.err_msg);
                }
            },
            error: function(xhr, status, errorThrown) {               // 通信失敗時にエラーを表示
                alert("通信に失敗しました。");
            }
        });
    });
</script>
{/literal}
