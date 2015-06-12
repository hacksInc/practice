<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="KPI ユーザー動向 継続率 - サイコパス管理ページ"}
<body>
    <link href="/css/admin/kpi/user.css" rel="stylesheet">
{include file="admin/log/cs/_part/log_cs_css.tpl"}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
    {include file="admin/common/breadcrumb.tpl"}

    <div class="row-fluid">
        {include file="admin/kpi/_part/kpi_menu.tpl"}

        <div class="span9 main-contents">
            <h2>ユーザー動向&nbsp;継続率</h2>
            {* 検索用入力エリア *}
            <div style="border: 1px solid #aaaaaa; padding:10px;margin:10px;width:600px;">
            {if count($errors)}
            <ul>
              {foreach from=$errors item=error}
               <div style="color: #ff0000;">{$error}</div>
              {/foreach}
            </ul>
            {/if}
            <form action="index" method="post" class="form-horizontal" id="formLogSearch">
                <div class="search-part">
                    検索日<span class="attention-require">(※)</span>：{form_input name="search_date_from" id="searchDateFrom" class="jquery-ui-datetimepicker"}
                </div>
                <div class="search-part">
                    集計項目：{form_input name="search_ua" id="searchUa"}<br />
                </div>
                <div style="width:580px;">
                    <div class="search-part-button">
                        <input type="button" value="表示する" class="btn btn-primary" id="btnSearchData">
                        <input type="hidden" value="" id="searchFlg" name="search_flg">
                        <input type="hidden" value="" id="start" name="start">
                    </div>
                </div>
            </form>
            </div>
            {if $app.search_flg == 1}
            <div class="">
                <div style="width:800px;padding:5px;">
                    <div style="margin-left:500px;">
                        <div class="kpi-list-float-box" style="width:450px">
                            <div>
                                <input type="button" value="前月" class="btn btn-primary" id="btnPrevMonth">
                                &nbsp;
                                <input type="button" value="次月" class="btn btn-primary" id="btnNextMonth">
                            </div>
                            <div>
                                &nbsp;
                            </div>
                            <div>
                            <form action="/admin/kpi/download" method="post" class="form-horizontal" id="formCsvDownload">
                                <input type="button" value="CSVダウンロード" class="btn btn-primary" id="btnCsvDownload">
                                <input type="hidden" value="" id="downloadFileName" name="file_name">
                            </form>
                            </div>
                            <div>
                                &nbsp;
                            </div>
                            {* <div>
                            <form action="/admin/kpi/user/print" method="post" class="form-horizontal" id="formKpiPrint">
                                <input type="button" value="印刷" class="btn btn-primary" id="btnKpiPrint">
                            </form>
                            </div> *}
                        </div>
                    </div>
                </div>
                {* 検索結果出力 *}
                {if $app.kpi_continuance_count==0}
                <div class="alert alert-warning">
                    <span>対象となるログは存在しません</span>
                </div>
                {else}
                <div style="width:800px;padding:5px;">
                    <div class="">
                        <span style="font-weight:bold;">{$app.kpi_date_from}</span>～<span style="font-weight:bold;">{$app.kpi_date_to}</span>の継続率
                    </div>
                </div>
                <div>
                    <div class="kpi-list-float-box">
                        <div class="kpi-list-title content-list-part-70">日付</div>
                        <div class="kpi-list-title content-list-part-50">当日</div>
                        {section name=kpi_list_title start=0 loop="Jm_KpiViewUserManager::CONTINUANCE_LIST_DAYS"|constant}
                        <div class="kpi-list-title content-list-part-70" name="kpi_after_date_{$smarty.section.kpi_list_title.iteration}">{$smarty.section.kpi_list_title.iteration}日目</div>
                        {/section}
                    </div>
                    {foreach from=$app.kpi_continuance_list key="key" item="continuance_data"}
                    <div class="kpi-list-float-box">
                        <div class="kpi-list-content content-list-part-70">{$continuance_data.date_install}<br>({$continuance_data.date_install_day})</div>
                        <div class="kpi-list-content content-list-part-50">{$continuance_data.count_download}</div>
                        {foreach from=$continuance_data.list key="key2" item="continuance_info" name="continuance"}
                            {if $continuance_info.rate != '-'}
                                <div class="kpi-list-content content-list-part-70 kpi-continuance-color{$continuance_info.bk_color}" name="kpi_after_date_{$smarty.foreach.continuance.index}" name="kpiAfterDate{$smarty.foreach.continuance.index}" >
                                        {$continuance_info.rate}%<br>
                                        {$continuance_info.count_login}
                                </div>
                                    {else}
                                <div class="kpi-list-content content-list-part-70" name="kpi_after_date_{$smarty.foreach.continuance.index}" name="kpiAfterDate{$smarty.foreach.continuance.index}" >
                                        {$continuance_info.rate}
                                </div>
                            {/if}
                        {/foreach}
                    </div>
                    {/foreach}
                {/if}
            </div><!--/content-->
            {/if}
        </div><!--/span-->
    </div><!--/row-->

    <hr>
    {include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
{include file="admin/log/cs/_part/log_cs_js.tpl"}
{include file="admin/_part/csvfile_download_js.tpl"}
<script src="/js/admin/kpi/user.js"></script>
{literal}
<script>
    $("#btnPrevMonth").click( function(){
        var kpi_date = new KpiDate( $("#searchDateFrom").val() );
        kpi_date.prevMonth();
        var prev_date = kpi_date.getDate();
        $("#searchDateFrom").val(prev_date);
        $("#searchFlg").val('1');
        $("#formLogSearch").submit();
    });
    $("#btnNextMonth").click( function(){
        var kpi_date = new KpiDate( $("#searchDateFrom").val() );
        kpi_date.nextMonth();
        var next_date = kpi_date.getDate();
        $("#searchDateFrom").val(next_date);
        $("#searchFlg").val('1');
        $("#formLogSearch").submit();
    });
    $("#btnKpiPrint").click( function(){
        alert("対象期間のKPIをpdfファイルに出力して印刷ます！(予定)\r\nただいま実装中なのです。もうちょっと待つのです！");
    });
</script>
{/literal}
</body>
</html>
