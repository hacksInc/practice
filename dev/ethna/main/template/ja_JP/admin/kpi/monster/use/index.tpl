<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="KPI モンスター使用率 - サイコパス管理ページ"}
<body>
    <link href="/css/admin/kpi/monster.css" rel="stylesheet">
{include file="admin/log/cs/_part/log_cs_css.tpl"}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
    {include file="admin/common/breadcrumb.tpl"}

    <div class="row-fluid">
        {include file="admin/kpi/_part/kpi_menu.tpl"}

        <div class="span9 main-contents">
            <h2>モンスター使用率</h2>
            {* 検索用入力エリア *}
            <div style="border: 1px solid #aaaaaa; padding: 10px; margin: 10px; width:600px;">
            {if count($errors)}
            <ul>
              {foreach from=$errors item=error}
               <div style="color: #ff0000;">{$error}</div>
              {/foreach}
            </ul>
            {/if}
            <form action="index" method="post" class="form-horizontal" id="formLogSearch">
                <div class="search-part">
                    検索日{*<span class="attention-require">(※)</span>*}：{form_input name="search_date_from" id="searchDateFrom" class="jquery-ui-datepicker"}&nbsp;～&nbsp;{form_input name="search_date_to" id="searchDateTo" class="jquery-ui-datepicker"}
                </div>
                <div class="search-part">
                    モンスターID：{form_input name="monster_id" id="monsterId"}<br />
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
                <div style="margin: 10px; width:600px;">
					<form action="/admin/kpi/download" method="post" class="form-horizontal" id="formCsvDownload" style="display:inline-block;">
						<input type="button" value="CSVダウンロード" class="btn btn-primary" id="btnCsvDownload">
						<input type="hidden" value="" id="downloadFileName" name="file_name">
					</form>
                </div>
				
                {* 検索結果出力 *}
				<div class="">
					<span style="font-weight:bold;">{$app.kpi_date_from}</span>～<span style="font-weight:bold;">{$app.kpi_date_to}</span>のモンスター使用率
				</div>
					
				{if count($app.kpi_monster_master) == 0}
					<div class="alert alert-warning">
						<span>対象となるモンスターは存在しません</span>
					</div>
				{else}
					<small>
						<table class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th>{$app.kpi_monster_master[0][0]}</th>
									<th>{$app.kpi_monster_master[0][1]}</th>
									<th>{$app.kpi_monster_master[0][2]}</th>
									<th>{$app.kpi_monster_master[0][3]}</th>
									<th>{$app.kpi_monster_master[0][4]}</th>
								</tr>
							</thead>
							<tbody>
							{section loop=$app.kpi_monster_master name=i start=1}
								<tr>
									<td>{$app.kpi_monster_master[i][0]}</td>
									<td>{$app.kpi_monster_master[i][1]}</td>
									<td>{$app.kpi_monster_master[i][2]}</td>
									<td>{$app.kpi_monster_master[i][3]}</td>
									<td>{$app.kpi_monster_master[i][4]}</td>
								</tr>
							{/section}
							</tbody>
						</table>
					</small>
				{/if}
				
				
                {if count($app.kpi_monster_use) == 0}
					<div class="alert alert-warning">
						<span>対象となる集計結果は存在しません</span>
					</div>
                {else}
                    <div class="kpi-list-float-box kpi-list-title-box content-list-part-1350">
                        <div class="kpi-list-title content-list-part-70">&nbsp;{$app.kpi_monster_use[0][0]}</div>
						<div class="kpi-list-sub-title-gold content-list-part-80">{$app.kpi_monster_use[0][1]}</div>
						<div class="kpi-list-sub-title-gold content-list-part-80">{$app.kpi_monster_use[0][2]}</div>
						<div class="kpi-list-sub-title-gold content-list-part-80">{$app.kpi_monster_use[0][3]}</div>
						<div class="kpi-list-sub-title-green content-list-part-80">{$app.kpi_monster_use[0][4]}</div>
						<div class="kpi-list-sub-title-green content-list-part-80">{$app.kpi_monster_use[0][5]}</div>
						<div class="kpi-list-sub-title-green content-list-part-80">{$app.kpi_monster_use[0][6]}</div>
                    </div>
					
					{section loop=$app.kpi_monster_use name=i start=1}
                    <div class="kpi-list-float-box kpi-list-content-box content-list-part-1350">
						<div class="kpi-list-content content-list-part-70">{$app.kpi_monster_use[i][0]}</div>
						<div class="kpi-list-content content-list-part-80">{$app.kpi_monster_use[i][1]}</div>
						<div class="kpi-list-content content-list-part-80">{$app.kpi_monster_use[i][2]}</div>
						<div class="kpi-list-content content-list-part-80">{$app.kpi_monster_use[i][3]}</div>
						<div class="kpi-list-content content-list-part-80">{$app.kpi_monster_use[i][4]}</div>
						<div class="kpi-list-content content-list-part-80">{$app.kpi_monster_use[i][5]}</div>
						<div class="kpi-list-content content-list-part-80">{$app.kpi_monster_use[i][6]}</div>
                    </div>
					{/section}
                {/if}
				
				<p class="text-warning">
					※ベースLv平均、スキルLv平均、流通量は日次バッチ実行時点（翌日午前5時過ぎ）の集計値です。<br>
					※リーダー使用のべ数、メンバー使用のべ数、ヘルプ使用のべ数は、深夜0時時点の集計値です。<br>
				</p>
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
{*
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
</script>
{/literal}
*}
</body>
</html>
