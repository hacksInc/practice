<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="アナウンスデータデプロイ制御 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>アナウンスデータデプロイ制御</h2>
			{if !$app.log_writable}
			    <p class="text-warning">
					<i class="icon-warning-sign"></i> 操作ログへ記録できない状態です。
				</p>
			{/if}
			
			対象ディレクトリ：{" "|implode:$app.directories}
			
			<h3>同期</h3>
			<p>

			{if Util::getEnv() == "dev"}
				{assign var="btn_name" value="STG反映" }
			{elseif Util::getEnv() == "stg"}
				{assign var="btn_name" value="商用反映" }
			{else}
				{assign var="btn_name" value="反映不可" }
			{/if}

			<div class="row-fluid">
				<div class="span3"><button class="btn rsync-makuo-button" id="rsync-button" {if $app.rsync_disabled}disabled{/if}>{$btn_name}</button><i class="icon-question-sign" data-original-title="STGで実行する機能になります。商用管理サーバへ反映を行ないます。"></i></div>
		        <div class="span3">前回反映</div>
		        <div class="span3" id="rsync-time">{if $app.last_rsync}{$app.last_rsync.time}{/if}</div>
		        <div class="span3" id="rsync-user">{if $app.last_rsync}{$app.last_rsync.user}{/if}</div>
			</div>
			</p>
			<p>
			<div class="row-fluid">
		        <div class="span3"><button class="btn rsync-makuo-button" id="makuo-button">デプロイ</button><i class="icon-question-sign" data-original-title="商用環境で実行する機能になります。動作としてはmakuoを行ない、webに反映する動作になります。"></i></div>
		        <div class="span3">前回デプロイ</div>
		        <div class="span3" id="makuo-time">{if $app.last_makuo}{$app.last_makuo.time}{/if}</div>
		        <div class="span3" id="makuo-user">{if $app.last_makuo}{$app.last_makuo.user}{/if}</div>
			</div>
			</p>
			
			{if $app.rsync_disabled}
				※商用反映はステージング環境でのみ動作します。<br>
			{/if}
			
			<div class="text-right">{a href="log/view"}＞ 操作ログ閲覧{/a}</div>

			<h3>バックアップ</h3>
			<p>
			<div class="row-fluid">
				<div class="span3">
					<form action="/psychopass_game/admin/api/tar/download" method="post" id="download-form">
						<input type="hidden" name="target" value="{$app.target}">
						<input type="submit" class="btn" value="ダウンロード" id="download-btn"><i class="icon-question-sign" data-original-title="対象ディレクトリをローカルPCへダウンロードできます。"></i>
					</form>
				</div>
			</div>
			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{include file="admin/_part/tar_download_js.tpl"}
</body>
</html>
