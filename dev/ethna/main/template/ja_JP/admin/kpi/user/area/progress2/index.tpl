<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="KPI ユーザー動向 エリア進捗(新) - サイコパス管理ページ"}
<body>
	<link href="/css/admin/kpi/user.css" rel="stylesheet">
{include file="admin/log/cs/_part/log_cs_css.tpl"}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/kpi/_part/kpi_menu.tpl"}

		<div class="span9 main-contents">
			<h2>ユーザー動向&nbsp;エリア進捗(新)</h2>
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
				<div class="search-part">
					集計項目：{form_input name="search_ua" id="searchUa"}<br />
				</div>
				<div class="search-part">
					クエストID：
					<select name="search_quest_id">
					{foreach from=$app.quest_list key=k item=v}
						<option value="{$v.quest_id}" {if $v.quest_id==$form.search_quest_id}selected{/if}>{$v.quest_id}:{$v.name}</option>
					{/foreach}
					</select>
				</div>
				<div style="width:580px;">
					<div class="search-part-button">
						<input type="button" value="検索する" class="btn btn-primary" id="btnSearchData">
						<input type="hidden" value="" id="searchFlg" name="search_flg">
						{if $app.search_flg!="1"}
							<input type="submit" value="CSVダウンロード" class="btn btn-primary" id="btnDownloadCsv" name="csv">
						{/if}
					</div>
				</div>
			</form>
			</div>
			{if $app.search_flg=="1"}
				
				{* 検索結果出力 *}
				<div class="download-part">
				<form action="csv" method="post">
					<input type="submit" value="CSVダウンロード" class="btn btn-primary" id="btnDownloadCsv">
					<input type="hidden" name="search_date_from" value="{$form.search_date_from}">
					<input type="hidden" name="search_date_to" value="{$form.search_date_to}">
					<input type="hidden" name="search_ua" value="{$form.search_ua}">
					<input type="hidden" name="search_quest_id" value="{$form.search_quest_id}">
				</form>
				</div>
				<div>
					<div class="search-list-content-box">
						<div class="search-list-content content-list-part-60">マップID</div>
						<div class="search-list-content content-list-part-80">クエストID</div>
						<div class="search-list-content content-list-part-120">クエスト名</div>
						<div class="search-list-content content-list-part-80">エリアID</div>
						<div class="search-list-content content-list-part-150">エリア名</div>
						<div class="search-list-content content-list-part-150">スタートユニーク人数</div>
						<div class="search-list-content content-list-part-150">クリアユニーク人数</div>
						<div class="search-list-content content-list-part-150">クリアユーザランク平均</div>
						<div class="search-list-content content-list-part-150">総コンティニュー回数</div>
					{*
						<div class="search-list-content content-list-part-150">コンティニュー総額</div>
						<div class="search-list-content content-list-part-150">コンティニュー額<br />(サービスメダル)</div>
						<div class="search-list-content content-list-part-150">コンティニュー額<br />(有償メダル)</div>
						<div class="search-list-content content-list-part-150">コンティニュー課金比率</div>
					*}
						<div class="search-list-content content-list-part-150">挑戦回数</div>
						<div class="search-list-content content-list-part-150">クリア回数</div>
						<div class="search-list-content content-list-part-150">コンティニュー無クリア回数</div>
						<div class="search-list-content content-list-part-150">コンティニュー有クリア回数</div>
						<div class="search-list-content content-list-part-150">クリア率</div>
						<div class="search-list-content content-list-part-150">リタイア回数</div>
						<div class="search-list-content content-list-part-150">リタイア率</div>
					</div>
					{foreach from=$app.quest_area key="i" item="quest_data"}
					<div class="search-list-content-box">
						<div class="search-list-content content-list-part-60">{$quest_data.map_id}</div>
						<div class="search-list-content content-list-part-80">{$quest_data.quest_id}</div>
						<div class="search-list-content content-list-part-120">{$quest_data.qname}</div>
						<div class="search-list-content content-list-part-80">{$quest_data.area_id}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.aname}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.start_uniq_user}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.clear_uniq_user}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.clear_rank_avg}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.total_continue}</div>
					{*
						<div class="search-list-content content-list-part-150">{$quest_data.continue_medal}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.continue_srv}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.continue_pay}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.continue_avg}</div>
					*}
						<div class="search-list-content content-list-part-150">{$quest_data.play_cnt}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.clear_cnt}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.clear_nocont_cnt}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.clear_cont_cnt}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.clear_avg}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.retire_cnt}</div>
						<div class="search-list-content content-list-part-150">{$quest_data.retire_avg}</div>
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
