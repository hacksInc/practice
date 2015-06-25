<script>
{literal}
    $('#dialogQuestInfo').dialog({
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
          $("#dialogQuestInfo").load($(this).dialog("option", "url"), null, function() {
          });
      }
    });
    $('.link-quest-info').click(function(){
      var play_id = $(this).attr('rel');
      if (play_id!="") {
        play_id = '?play_id=' + encodeURIComponent(play_id);
      }
      $("#dialogQuestInfo").dialog("option", "url", "/admin/log/cs/quest/playdata/index"+play_id);
      $('#dialogQuestInfo').dialog('open');
      return false;
    });
{/literal}
</script>
