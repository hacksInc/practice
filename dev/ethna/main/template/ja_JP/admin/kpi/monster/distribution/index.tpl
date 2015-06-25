<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="KPI モンスター流通量 - サイコパス管理ページ"}
<body>
	<link href="/css/admin/kpi/monster.css" rel="stylesheet">
{include file="admin/log/cs/_part/log_cs_css.tpl"}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/kpi/_part/kpi_menu.tpl"}

		<div class="span9 main-contents">
			<h2>モンスター流通量</h2>
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
			{*
				<div style="margin: 10px; width:600px;">
					<form action="/admin/kpi/download" method="post" class="form-horizontal" id="formCsvDownload" style="display:inline-block;">
						<input type="button" value="CSVダウンロード" class="btn btn-primary" id="btnCsvDownload">
						<input type="hidden" value="" id="downloadFileName" name="file_name">
					</form>
				</div>
			*}
				{if $app.monster_name==""}
					<div class="">
						<span style="font-weight:bold;">指定されたモンスターは存在しません</span>
					</div>
				{else}
					{* 検索結果出力 *}
					<div class="">
						<span style="font-weight:bold;">{$form.monster_id}</span>　<span style="font-weight:bold;">{$app.monster_name}</span>の現在の流通量
					</div>
	
						<div class="kpi-list-float-box kpi-list-title-box content-list-part-1350">
							<div class="kpi-list-title content-list-part-70">&nbsp;ユニット</div>
							<div class="kpi-list-sub-title-gold content-list-part-80">数</div>
						</div>
						
						{foreach from=$app.data key=k item=v}
						<div class="kpi-list-float-box kpi-list-content-box content-list-part-1350">
							<div class="kpi-list-content content-list-part-70">{$k}</div>
							<div class="kpi-list-content content-list-part-80">{$v}</div>
						</div>
						{/foreach}
						<div class="kpi-list-float-box kpi-list-content-box content-list-part-1350">
							<div class="kpi-list-content content-list-part-70">合計</div>
							<div class="kpi-list-content content-list-part-80">{$app.total}</div>
						</div>
					</div><!--/content-->
				{/if}
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
