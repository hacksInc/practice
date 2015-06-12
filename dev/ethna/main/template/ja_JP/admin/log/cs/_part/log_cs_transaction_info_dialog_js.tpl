<script>
{literal}
    $('#dialogTransactionInfo').dialog({
      autoOpen: false,
      title: '{/literal}{$app.dialog_title}{literal}',
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
          $("#dialogTransactionInfo").load($(this).dialog("option", "url"), null, function() {
          });
      }
    });
    $('.link-transaction-info').click(function(){
      var transaction_id = $(this).attr('rel');
      if (transaction_id!="") {
        transaction_id = '?api_transaction_id=' + encodeURIComponent(transaction_id);
      }
      $("#dialogTransactionInfo").dialog("option", "url", "{/literal}{$app.dialog_url}{literal}"+transaction_id);
      $('#dialogTransactionInfo').dialog('open');
      return false;
    });
{/literal}
</script>
