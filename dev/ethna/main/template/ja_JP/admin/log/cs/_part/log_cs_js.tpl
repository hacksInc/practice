{literal}
<script>
    $("#btnSearchData").click( function() {
        $("#searchFlg").val('1');
        $("#formLogSearch").submit();
    });
    $(".list-pager").click( function() {
        var rel = $(this).attr('rel');
        $("#start").val(rel);
        $("#searchFlg").val('1');
        $("#formLogSearch").submit();
    });
</script>
{/literal}
