<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="KPI ガチャ集計 - サイコパス管理ページ"}
<body>
	<link href="/css/admin/kpi/user.css" rel="stylesheet">
{include file="admin/log/cs/_part/log_cs_css.tpl"}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/kpi/_part/kpi_menu.tpl"}

		<div class="span9 main-contents">
			<h2>ユーザー毎&nbsp;ガチャ回数</h2>
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
					検索日：{form_input name="search_date_from" id="searchDateFrom" class="jquery-ui-datetimepicker"} ～ {form_input name="search_date_to" id="searchDateTo" class="jquery-ui-datetimepicker"}
				</div>
				<div class="search-part" style="height:100px;">
					ガチャID：
					<select name="search_gacha_ids[]" multiple>
					{foreach from=$app.gacha_list key=k item=v}
						<option value="{$v.gacha_id}" {if $v.selected==1}selected{/if}>{$v.gacha_id}:{$v.comment}</option>
					{/foreach}
					</select>　※複数選択可(Ctrlキーを押しながら)
				</div>
				<div style="width:580px;">
					<div class="search-part-button">
						<input type="button" value="検索する" class="btn btn-primary" id="btnSearchData">
						<input type="hidden" value="1" id="searchFlg" name="search_flg">
						<input type="submit" value="CSVダウンロード" class="btn btn-primary" id="btnDownloadCsv" name="csv">
					</div>
				</div>
			</form>
			</div>
			{if $app.search_flg=="1"}
				{* 検索結果出力 *}
				{*
				<div class="download-part">
				<form action="csv" method="post">
					<input type="submit" value="CSVダウンロード" class="btn btn-primary" id="btnDownloadCsv">
					<input type="hidden" name="search_date_from" value="{$form.search_date_from}">
					<input type="hidden" name="search_date_to" value="{$form.search_date_to}">
					<input type="hidden" name="search_quest_id" value="{$form.search_quest_id}">
				</form>
				</div>
				*}
				<div class="">
					<span style="font-weight:bold;">{$form.search_date_from}</span>～<span style="font-weight:bold;">{$form.search_date_to}</span>のユーザー毎 ガチャ回数
				</div>
				
				<div>
					<div class="kpi-list-float-box kpi-list-title-box">
						{foreach from=$app.dataname key="n" item="datan"}
							<div class="kpi-list-title content-list-part-80">{$datan}</div>
						{/foreach}
					</div>
					{foreach from=$app.data key="i" item="datas"}
						<div class="kpi-list-float-box">
							{foreach from=$datas key="ii" item="data"}
								<div class="kpi-list-content content-list-part-80" align="right">{$data}</div>
							{/foreach}
						</div>
					{/foreach}
				</div>
			{/if}
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
<div class="" id="dialogTransactionInfo">
</div>
<div class="" id="dialogQuestInfo">
</div>
{include file="admin/common/script.tpl" datepicker="jquery"}
{include file="admin/log/cs/_part/log_cs_csv_download_js.tpl"}
{include file="admin/log/cs/_part/log_cs_js.tpl"}
{include file="admin/log/cs/_part/log_cs_quest_info_dialog_js.tpl"}
{include file="admin/log/cs/_part/log_cs_transaction_info_dialog_js.tpl"}
</body>
</html>
