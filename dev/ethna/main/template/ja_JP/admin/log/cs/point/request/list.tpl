<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ポイント管理アクセスログ - サイコパス管理ページ"}
<body>
{include file="admin/log/cs/_part/log_cs_css.tpl"}
{literal}
<style type="text/css">
  .json-modal-body { word-wrap: break-word; }
</style>
{/literal}

{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
    {include file="admin/common/breadcrumb.tpl"}

    <div class="row-fluid">
        {include file="admin/log/cs/_part/log_menu.tpl"}
        {* include file="admin/common/sidebar.tpl" *}

        {*<div class="span9 main-contents">*}
			{if $form.search_type == constant("Jm_LogdataViewPointManager::SEARCH_TYPE_USER_ID")}
	            <h2>ポイント管理アクセスログ</h2>
			{elseif $form.search_type == constant("Jm_LogdataViewPointManager::SEARCH_TYPE_STS_NG")}
	            <h2>ポイント管理アクセスNGログ</h2>
			{/if}
			
			<div class="row-fluid">
				<div class="span2">検索日時：</div>
				<div class="span8">{$form.search_date_from} ～ {$form.search_date_to}</div>
			</div>
			{if $form.search_type == constant("Jm_LogdataViewPointManager::SEARCH_TYPE_USER_ID")}
				<div class="row-fluid">
					<div class="span2">ユーザーID：</div>
					<div class="span8">{$form.search_user_id}</div>
				</div>
			{/if}
			<div class="row-fluid">
				{$app.point_log_count}件のログが抽出されました
			</div>
			
			<div class="text-right">
				<form action="/admin/log/cs/point/request/download" method="post" class="form-horizontal" id="formCsvDownloadDirect">
					<input type="submit" value="CSVダウンロード" class="btn btn-primary" id="btnCsvDownloadDirect">
					<input type="hidden" name="search_date_from" value="{$form.search_date_from}">
					<input type="hidden" name="search_date_to" value="{$form.search_date_to}">
					<input type="hidden" name="search_type" value="{$form.search_type}">
					{if $form.search_type == constant("Jm_LogdataViewPointManager::SEARCH_TYPE_USER_ID")}
						<input type="hidden" name="search_user_id" value="{$form.search_user_id}">
					{/if}
				</form>
			</div>

			<div class="text-center">
				{$app_ne.pager.all}
			</div>

			{* 検索結果出力 *}
			{foreach from=$app.point_log_list item="row" name="loop1" key="i"}
				<div class="row-fluid" style="{if $smarty.foreach.loop1.first}margin-top: 5px; {/if}padding: 5px; border-top: solid 1px; border-left: solid 1px; border-right: solid 1px; {if $smarty.foreach.loop1.last}border-bottom: solid 1px; margin-bottom: 5px;{/if}">
					<div class="row-fluid">
						<div class="span1">ID</div>
						<div class="span2">{$row.id}</div>
						<div class="span2">ゲームトランザクションID</div>
						<div class="span3">{$row.game_transaction_id}</div>
						<div class="span1">登録日</div>
						<div class="span3">{$row.date_created}</div>
					</div>
					<div class="row-fluid">
						<div class="span1">
							<div class="row-fluid">
								<div class="span12">ユーザーID</div>
							</div>
							<div class="row-fluid">
								<div class="span12">アクセス元IP</div>
							</div>
						</div>
						<div class="span2">
							<div class="row-fluid">
								<div class="span12">{$row.user_id}</div>
							</div>
							<div class="row-fluid">
								<div class="span12">{$row.remote_addr}</div>
							</div>
						</div>
						<div class="span2">
							<div class="row-fluid">
								<div class="span12">実行アクション</div>
							</div>
							<div class="row-fluid">
								<div class="span12">実行結果</div>
							</div>
						</div>
						<div class="span3">
							<div class="row-fluid">
								<div class="span12">{$row.action}</div>
							</div>
							<div class="row-fluid">
								<div class="span12">{$row.result_short}...</div>
							</div>
						</div>
						<div class="span1">レシート</div>
						<div class="span1">{$row.receipt_check}</div>
						<div class="span1">
							<button type="button" class="btn btn-mini" data-toggle="modal" data-target="#gameArg{$i}">アプリデータ表示</button>
							<div id="gameArg{$i}" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								<h3 id="myModalLabel">game_arg</h3>
								</div>
								<div class="modal-body json-modal-body">
								<p>{$row.game_arg}</p>
								</div>
								<div class="modal-footer">
								<button class="btn" data-dismiss="modal" aria-hidden="true">閉じる</button>
								</div>
							</div>
						</div>
						<div class="span1">
							<button type="button" class="btn btn-mini" data-toggle="modal" data-target="#result{$i}">送信データ表示</button>
							<div id="result{$i}" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
								<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
								<h3 id="myModalLabel">result</h3>
								</div>
								<div class="modal-body json-modal-body">
								<p>{$row.result}</p>
								</div>
								<div class="modal-footer">
								<button class="btn" data-dismiss="modal" aria-hidden="true">閉じる</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			{/foreach}
			
			<div class="text-center">
				{$app_ne.pager.all}
			</div>
			
        {*</div><!--/span-->*}
    </div><!--/row-->

    <hr>
    {include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
{include file="admin/log/cs/_part/log_cs_csv_download_direct_js.tpl"}
</body>
</html>
