<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="KPI ユーザー動向 バトル進捗 - サイコパス管理ページ"}
<body>
    <link href="/css/admin/kpi/user.css" rel="stylesheet">
{include file="admin/log/cs/_part/log_cs_css.tpl"}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
    {include file="admin/common/breadcrumb.tpl"}

    <div class="row-fluid">
        {include file="admin/kpi/_part/kpi_menu.tpl"}

        <div class="span9 main-contents">
            <h2>ユーザー動向&nbsp;バトル進捗</h2>
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
                    マップ<span class="attention-require">(※)</span>：{form_input name="search_map_id" id="searchMapId"}<br />
                </div>
                <div class="search-part">
                    クエスト：{form_input name="search_quest_id" id="searchQuestId"}<br />
                </div>
                <div class="search-part">
                    集計項目<span class="attention-require">(※)</span>：{form_input name="search_ua" id="searchUa"}<br />
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
                                <input type="button" value="前日" class="btn btn-primary" id="btnPrevDay">
                                &nbsp;
                                <input type="button" value="翌日" class="btn btn-primary" id="btnNextDay">
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
                {if $app.kpi_battle_progress_count==0}
                <div class="alert alert-warning">
                    <span>対象となるログは存在しません</span>
                </div>
                {else}
                <div style="width:800px;padding:5px;">
                    <div class="">
                        <span style="font-weight:bold;">{$app.kpi_date_from}</span>のバトル進捗
                    </div>
                </div>
                <div>
                    <div class="kpi-list-float-box">
                        <div class="kpi-list-title content-list-part-150">マップ名</div>
                        <div class="kpi-list-title content-list-part-100">クエストID</div>
                        <div class="kpi-list-title content-list-part-150">クエスト名</div>
                        <div class="kpi-list-title content-list-part-100">エリアID</div>
                        <div class="kpi-list-title content-list-part-150">エリア名</div>
                        <div class="kpi-list-title content-list-part-50">バトル<br />番号</div>
                        <div class="kpi-list-title content-list-part-70">モンスター<br />出現数</div>
                        <div class="kpi-list-title content-list-part-70">リタイア数</div>
                        <div class="kpi-list-title content-list-part-100">コンティニュー数<br />(有料)</div>
                        <div class="kpi-list-title content-list-part-100">コンティニュー数<br />(無料)</div>
                        <div class="kpi-list-title content-list-part-100">コンティニュー数<br />(総計)</div>
                        <div class="kpi-list-title content-list-part-100">課金率</div>
                    </div>
                    {foreach from=$app.kpi_battle_progress_list key="key" item="battle_data"}
                    <div class="kpi-list-float-box">
                        <div class="kpi-list-content content-list-part-150">
                            [{$battle_data.map_id}]&nbsp;{$battle_data.map_name}</div>
                        <div class="kpi-list-content content-list-part-100">{$battle_data.quest_id}</div>
                        <div class="kpi-list-content content-list-part-150">{$battle_data.quest_name}</div>
                        <div class="kpi-list-content content-list-part-100">{$battle_data.area_id}</div>
                        <div class="kpi-list-content content-list-part-150">{$battle_data.area_name}</div>
                        <div class="kpi-list-content content-list-part-50">{$battle_data.lose_battle_num}</div>
                        <div class="kpi-list-content content-list-part-70">{$battle_data.count_battle_monster}</div>
                        <div class="kpi-list-content content-list-part-70">{$battle_data.count_retire}</div>
                        <div class="kpi-list-content content-list-part-100">{$battle_data.count_continue_pay}</div>
                        <div class="kpi-list-content content-list-part-100">{$battle_data.count_continue_free}</div>
                        <div class="kpi-list-content content-list-part-100">{$battle_data.count_continue_total}</div>
                        <div class="kpi-list-content content-list-part-100">{$battle_data.continue_charge_rate}%</div>
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
    $("#btnPrevDay").click( function(){
        var kpi_date = new KpiDate( $("#searchDateFrom").val() );
        kpi_date.prevDay();
        var prev_date = kpi_date.getDate();
        $("#searchDateFrom").val(prev_date);
        $("#searchFlg").val('1');
        $("#formLogSearch").submit();
    });
    $("#btnNextDay").click( function(){
        var kpi_date = new KpiDate( $("#searchDateFrom").val() );
        kpi_date.nextDay();
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
