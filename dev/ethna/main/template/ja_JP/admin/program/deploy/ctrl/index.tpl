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
			
			対象ディレクトリ：{" "|implode:$app.directories}
			
			<h3>同期</h3>
			<p>
			<div class="row-fluid">
				<div class="span3"><button class="btn rsync-makuo-button" id="rsync-button" {if $app.rsync_disabled}disabled{/if}>商用反映</button><i class="icon-question-sign" data-original-title="ステージング環境管理サーバから商用環境管理サーバへ同期します。ステージング環境で実行する機能です。"></i></div>
		        <div class="span3">前回反映</div>
		        <div class="span3" id="rsync-time">{if $app.last_rsync}{$app.last_rsync.time}{/if}</div>
		        <div class="span3" id="rsync-user">{if $app.last_rsync}{$app.last_rsync.user}{/if}</div>
			</div>
			</p>
			
			<hr>
			<p>
			<div class="row-fluid">
		        <div class="span3"><button class="btn rsync-makuo-button" id="makuo-button">デプロイ</button><i class="icon-question-sign" data-original-title="管理サーバからWebサーバへ同期します。ステージング・商用環境でそれぞれ実行する機能です。開発環境で実行することも可能です。"></i></div>
		        <div class="span3">前回デプロイ</div>
		        <div class="span3" id="makuo-time">{if $app.last_makuo}{$app.last_makuo.time}{/if}</div>
		        <div class="span3" id="makuo-user">{if $app.last_makuo}{$app.last_makuo.user}{/if}</div>
			</div>
			</p>
			
			<hr>
			<p>
			{strip}
			<div class="row-fluid">
		        <div class="span1">Path</div>
				<div class="span10">
					<input type="text" id="svn-path-input">
					<i class="icon-question-sign" data-original-title="例：/tags/web_1_2_xx_20140422 /branches/web_1_2_xx_x_20140325 /trunk/web"></i>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span1">Revision</div>
				<div class="span10">
					<input type="text" id="svn-revision-input">
					<i class="icon-question-sign" data-original-title="省略可"></i>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span3">
					<button class="btn rsync-makuo-button" id="svn-button" {if $app.svn_disabled}disabled{/if}>SVN反映</button>
					<i class="icon-question-sign" data-original-title="SVNレポジトリから管理サーバへ同期します。ステージング環境で実行する機能です。開発環境で実行することも可能です。"></i>
				</div>
			</div>
			{/strip}
			<div class="row-fluid">
		        <div class="span3"></div>
		        <div class="span3">前回反映</div>
		        <div class="span3" id="svn-time">{if $app.last_svn}{$app.last_svn.time}{/if}</div>
		        <div class="span3" id="svn-user">{if $app.last_svn}{$app.last_svn.user}{/if}</div>
			</div>
			</p>

			<hr>
			{if $app.rsync_disabled}
				※商用反映はステージング環境でのみ動作します。<br>
			{/if}
			
			{if $app.svn_disabled}
				※SVN反映は開発環境またはステージング環境で動作します。<br>
			{else}
				※SVN反映は実行後にバックアップディレクトリ{*とテンポラリディレクトリ*}が残ります。これらは手動で削除して下さい。<br>
				　バックアップディレクトリ：{$smarty.const.BASE}/～.～.bak<br>
{*
				　テンポラリディレクトリ　：{$smarty.const.BASE}/tmp/svn～<br>
*}
				<strong>※SVN反映ではデプロイ(makuo)は実行しません。別途実行する必要があります。</strong><br>
			{/if}
			
			<div class="text-right">{a href="log/view"}＞ 操作ログ閲覧{/a}</div>

			<h3>バックアップ</h3>
			<p>
			<div class="row-fluid">
				<div class="span3">
					<form action="/admin/api/tar/download" method="post" id="download-form">
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
