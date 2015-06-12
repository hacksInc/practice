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

            <h3>商用反映 <small>(rsync)</small></h3>
            <p>
            <small>管理サーバ から {env_label env="pro"}管理サーバ へ反映します。</small>
            </p>
            
            <hr>
            <h4>比較</h4>
			<p>
			{strip}
			<form action="diff/dest" method="post">
				<div class="row-fluid">
                    {strip}
                    対象ディレクトリ：
                    {foreach from=$app.dest_directories item="dir"}
                        <input type="hidden" name="dest_directories[]" value="{$dir}">
                        {$dir}&nbsp;
                    {/foreach}
                    {/strip}            
				</div>
				<div class="row-fluid">
					<div class="span3">
						<input type="submit" class="btn" value="比較する" {if $app.dest_disabled}disabled{/if}>
                        <i class="icon-question-sign" data-original-title="商用環境の対象ディレクトリをテンポラリディレクトリへscpした上で、diffを実行します。"></i>
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
				<div class="span3"><button class="btn rsync-makuo-button" id="rsync-button" {if $app.rsync_disabled}disabled{/if}>同期する</button><i class="icon-question-sign" data-original-title="rsyncを実行します。"></i></div>
			</div>
			</p>

            <p>
			{if $app.rsync_disabled}
				※商用反映はステージング環境でのみ動作します。<br>
			{else}
				<strong>※別途、デプロイ(makuo)を商用環境の管理画面で実行して下さい。</strong><br>
			{/if}
            </p>

            <div class="text-right">
                前回同期：
		        <span id="rsync-time">{if $app.last_rsync}{$app.last_rsync.time}{else}unknown{/if}</span>&nbsp;
		        <span id="rsync-user">{if $app.last_rsync}{$app.last_rsync.user}{/if}</span><br>
                {a href="ctrl/log/view"}＞ 操作ログ閲覧{/a}
            </div>

		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{include file="admin/_part/tar_download_js.tpl"}
</body>
</html>
