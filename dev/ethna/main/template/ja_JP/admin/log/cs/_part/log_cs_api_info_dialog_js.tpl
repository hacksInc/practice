<script>
{literal}
    $('#dialogApiInfo').dialog({
      autoOpen: false,
      title: 'クエスト情報詳細',
      width: "800px",
      closeOnEscape: false,
      modal: true,
      position: {
          my: "center",
          at: "center",
          of: ".main-contents"
      },
      buttons: {
        "閉じる": function(){
          $(this).dialog('close');
        }
      },
      open: function(){
          $("#dialogApiInfo").load($(this).dialog("option", "url"), null, function() {
          });
      }
    });
    $('.link-quest-info').click(function(){
      var api_transaction_id = $(this).attr('rel');
      if (api_transaction_id!="") {
        api_transaction_id = '?api_transaction_id=' + encodeURIComponent(api__transaction_id);
      }
      $("#dialogApiInfo").dialog("option", "url", "/admin/log/cs/info"+api_transaction_id);
      $('#dialogApiInfo').dialog('open');
      return false;
    });
{/literal}
</script>
