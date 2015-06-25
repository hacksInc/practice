<!DOCTYPE html>
<html lang="ja">
	{include file="admin/common/head.tpl" title="フォト取得履歴 - サイコパス管理ページ"}

	{include file="admin/log/cs/_part/log_cs_css.tpl"}

{literal}
<style>
.search-list-title-box, .search-list-content-box { clear: both; }
.search-list-title, .search-list-content { float: left; }
.search-list-content { height: 30px; }
</style>
{/literal}
	<body>
		{include file="admin/common/navbar.tpl"}
		<div class="container-fluid">
			{include file="admin/common/breadcrumb.tpl"}

			<div class="row-fluid">
				{include file="admin/common/sidebar.tpl"}

				<div class="span9 main-contents">
					<h2>フォト取得履歴一覧</h2>
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
							{* <div class="search-part"> *}
								{*    フォト取得種類：{form_input name="search_photo_id" id="searchPhotoId"}<br /> *}
								{* </div> *}
							<div class="search-part search-part-name">
								<div>
									ニックネーム：{form_input name="search_name" id="searchName"}
									<label for="searchNameOption" style="padding:3px; margin-top:0; display: inline;">
										<input id="searchNameOption" type="checkbox" name="search_name_option" value="1" {if $app.name_option=="1"}checked{/if}> 完全一致検索する
									</label>
								</div>
							</div>
							<div class="search-part">
								ユーザーID：{form_input name="search_pp_id" id="searchUserId"}<br />
							</div>
							<div style="width:580px;">
								<div class="search-part-button">
									<input type="button" value="検索する" class="btn btn-primary" id="btnSearchData">
									<input type="hidden" value="" id="searchFlg" name="search_flg">
									<input type="hidden" value="" id="start" name="start">
								</div>
							</div>
						</form>
					</div>
					{if $app.search_flg=="1"}
					{* 検索結果出力 *}
					{if $app.photo_log_count==0}
					<div class="alert alert-warning">
						<span>対象となるログは存在しません</span>
					</div>
					{elseif $app.photo_log_count==-1}
					<div class="alert alert-danger">
						対象ログ件数が1万件を超えます。条件を変えて再度絞込みを行ってください。
					</div>
					{else}
					<div style="padding:5px;">
						<div class="search-list-title-box">
							<div>
								<span>{$app.photo_log_count}件のログが抽出されました</span>
							</div>
							<div style="margin-left:600px;">
								<form action="/psychopass_game/admin/log/cs/download" method="post" class="form-horizontal" id="formCsvDownload">
									<input type="button" value="CSVダウンロード" class="btn btn-primary" id="btnCsvDownload">
									<input type="hidden" value="" id="downloadFileName" name="file_name">
								</form>
							</div>
						</div>
					</div>
					<div style="width:800px;">
						<div style="margin-left:auto;margin-right:auto;">
							{if $app.hasprev}
							<a href="#" rel="0" class="list-pager">最初</a>&nbsp;<a href="#" rel="{$app.prev}" class="list-pager">&lt;&lt;</a>
							{else}
							最初&nbsp;&lt;&lt;
							{/if}
							&nbsp;
							{foreach from=$app.pager item=page}
							{if $page.offset == $app.current}
							<strong>{$page.index}</strong>
							{else}
							<a href="#" rel="{$page.offset}" class="list-pager">{$page.index}</a>
							{/if}
							&nbsp;
							{/foreach}
							{if $app.hasnext}
							<a href="#" rel="{$app.next}" class="list-pager">&gt;&gt;</a>&nbsp;<a href="#" rel="{$app.last}" class="list-pager">最後</a>
							{else}
							&gt;&gt;&nbsp;最後
							{/if}
						</div>
					</div>
					<div>
						<div class="search-list-title-box">
							<div class="search-list-title content-list-part-150">フォトID</div>
							<div class="search-list-title content-list-part-170">初取得日時</div>
							<div class="search-list-title content-list-part-150">現Lv</div>
						</div>
						{foreach from=$app.photo_log_list key="i" item="log_data"}
						<div class="search-list-content-box">
							<div class="search-list-content content-list-part-150">{$log_data.photo_id}</div>
							<div class="search-list-content content-list-part-170">{$log_data.date_created}</div>
							<div class="search-list-content content-list-part-150">{$log_data.photo_lv}</div>
						</div>
						{/foreach}
					</div>
					{/if}
					{/if}
				</div><!--/span-->
			</div><!--/row-->

			<hr>
			{include file="admin/common/footer.tpl"}
		</div><!--/.fluid-container-->
		<div class="" id="dialogTransactionInfo">
		</div>
		{include file="admin/common/script.tpl" datepicker="jquery"}
		{include file="admin/log/cs/_part/log_cs_csv_download_js.tpl"}
		{include file="admin/log/cs/_part/log_cs_transaction_info_dialog_js.tpl"}
		{include file="admin/log/cs/_part/log_cs_js.tpl"}
	</body>
</html>
