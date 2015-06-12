<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="KPI アイテム ガチャ毎課金率 - サイコパス管理ページ"}
<body>
    <link href="/css/admin/kpi/item.css" rel="stylesheet">
{include file="admin/log/cs/_part/log_cs_css.tpl"}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
    {include file="admin/common/breadcrumb.tpl"}

    <div class="row-fluid">
        {include file="admin/kpi/_part/kpi_menu.tpl"}

        <div {*class="span9 main-contents"*}>
            <h2>アイテム&nbsp;ガチャ毎課金率</h2>
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
                    検索日<span class="attention-require">(※)</span>：{form_input name="search_date_from" id="searchDateFrom" class="jquery-ui-datepicker"}{* &nbsp;～&nbsp;{form_input name="search_date_to" id="searchDateTo" class="jquery-ui-datepicker"} *}
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
                <div style="width:900px;padding:5px;">
                    <div style="margin-left:600px;">
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
                {if $app.kpi_gacha_charge_count==0}
                <div class="alert alert-warning">
                    <span>対象となる集計結果は存在しません</span>
                </div>
                {else}
                    <div class="">
                        <span style="font-weight:bold;">{$app.kpi_date_from}</span>～<span style="font-weight:bold;">{$app.kpi_date_to}</span>のアイテム毎 課金率
                    </div>
                <div>
                    <div class="kpi-list-float-box kpi-list-title-box">
                        <div class="kpi-list-title content-list-part-70">&nbsp;日付</div>
                        {foreach from=$app.kpi_gacha_name_list key="name_key" item="name_data"}
                        <div class="kpi-list-title content-list-part-320">
                            <div class="kpi-list-sub-title-gold content-list-part-320">{$name_data}</div>
                            <div class="kpi-list-float-box">
                                <div class="kpi-list-sub-title-gold content-list-part-80">有料ﾒﾀﾞﾙ額</div>
                                <div class="kpi-list-sub-title-gold content-list-part-80">無料ﾒﾀﾞﾙ額</div>
                                <div class="kpi-list-sub-title-gold content-list-part-80">総ﾒﾀﾞﾙ額</div>
                                <div class="kpi-list-sub-title-gold content-list-part-80">課金率</div>
                            </div>
                        </div>
                        {/foreach}
                    </div>
                    {foreach from=$app.kpi_gacha_charge_list key="key" item="charge_data"}
                    <div class="kpi-list-content-box">
                       <div class="kpi-list-float-box">
                            <div class="kpi-list-content content-list-part-70">
                                {$charge_data.date_gacha_tally}<br />
                                ({$charge_data.date_gacha_tally_day})
                            </div>
                            {*{foreach from=$charge_data.list key="list_key" item="list_data"}
                            <div class="kpi-list-content content-list-part-80">{$list_data.count_item_pay}</div>
                            <div class="kpi-list-content content-list-part-80">{$list_data.count_item_free}</div>
                            <div class="kpi-list-content content-list-part-80">{$list_data.count_item_total}</div>
                            <div class="kpi-list-content content-list-part-80">{$list_data.gacha_charge_rate}%</div>
                            {/foreach}*}
	                        {foreach from=$app.kpi_gacha_name_list key="name_key" item="name_data"}
								{if isset($charge_data.list.$name_key)}
								    <div class="kpi-list-content content-list-part-80">{$charge_data.list.$name_key.count_item_pay}</div>
						            <div class="kpi-list-content content-list-part-80">{$charge_data.list.$name_key.count_item_free}</div>
				                    <div class="kpi-list-content content-list-part-80">{$charge_data.list.$name_key.count_item_total}</div>
		                            <div class="kpi-list-content content-list-part-80">{$charge_data.list.$name_key.gacha_charge_rate}%</div>
								{else}
								    <div class="kpi-list-content content-list-part-80">&nbsp;</div>
						            <div class="kpi-list-content content-list-part-80">&nbsp;</div>
				                    <div class="kpi-list-content content-list-part-80">&nbsp;</div>
		                            <div class="kpi-list-content content-list-part-80">&nbsp;</div>
								{/if}
							{/foreach}
                        </div>
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
