<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="デプロイ制御 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>プログラムデプロイ制御</h2>
			{if !$app.log_writable}
			    <p class="text-warning">
					<i class="icon-warning-sign"></i> 操作ログへ記録できない状態です。
				</p>
			{/if}
            
            <h3>デプロイ <small>(makuo)</small></h3>
            <p>
            <small>管理サーバ から Webサーバ へ同期します。</small>
            </p>
            
            <hr>
            <h4>比較</h4>
			<p>
			{strip}
			<form action="diff/makuo" method="post">
				<div class="row-fluid">
                    対象ディレクトリ：
                    {foreach from=$app.makuo_directories item="dir"}
                        <input type="hidden" name="makuo_directories[]" value="{$dir}">
                        {$dir}&nbsp;
                    {/foreach}
				</div>
				<div class="row-fluid">
					<div class="span3">
						<input type="submit" class="btn" value="比較する">
                        <i class="icon-question-sign" data-original-title="makuo -n -d で未デプロイのファイルを検出します。同期は行いません。"></i>
					</div>
				</div>
			</form>
			{/strip}
			</p>
            
            <hr>
			<h4>同期</h4>
			対象ディレクトリ：{" "|implode:$app.directories}
			<p>
			<div class="row-fluid">
		        <div class="span3"><button class="btn rsync-makuo-button" id="makuo-button">同期する</button><i class="icon-question-sign" data-original-title="makuo -g -d で同期を行います。"></i></div>
			</div>
            <div class="text-right">
                前回同期：
		        <span id="makuo-time">{if $app.last_makuo}{$app.last_makuo.time}{else}unknown{/if}</span>&nbsp;
		        <span id="makuo-user">{if $app.last_makuo}{$app.last_makuo.user}{/if}</span><br>
            </div>
			</p>
			
			<div class="text-right">{a href="ctrl/log/view"}＞ 操作ログ閲覧{/a}</div>

		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{include file="admin/_part/tar_download_js.tpl"}
</body>
</html>
