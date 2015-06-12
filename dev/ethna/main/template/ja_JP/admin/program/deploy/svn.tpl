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
			
            <h3>SVN反映 <small>(svn export)</small></h3>
            <p>
            <small>SVNレポジトリ から 管理サーバ へ反映します。</small>
            </p>

            <hr>
            <h4>比較</h4>
			<p>
			{strip}
			<form action="diff/svn" method="post">
				<div class="row-fluid">
                    {strip}
                    対象ディレクトリ：
                    {foreach from=$app.svn_directories item="dir"}
                        <input type="hidden" name="svn_directories[]" value="{$dir}">
                        {$dir}&nbsp;
                    {/foreach}
                    {/strip}
				</div>
				<div class="row-fluid">
					<div class="span1">Path</div>
					<div class="span10">
						<input type="text" name="path">
						<i class="icon-question-sign" data-original-title="例：/tags/web_1_2_xx_20140422 /branches/web_1_2_xx_x_20140325 /trunk/web"></i>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span1">Revision</div>
					<div class="span10">
						<input type="text" name="revision">
						<i class="icon-question-sign" data-original-title="省略可"></i>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span3">
						<input type="submit" class="btn" value="比較する{*SVN比較*}" {if $app.svn_disabled}disabled{/if}>
                        <i class="icon-question-sign" data-original-title="svn export と diff を実行します。"></i>
					</div>
				</div>
			</form>
			{/strip}
			</p>

            <hr>
            <h4>同期</h4>
			対象ディレクトリ：{" "|implode:$app.directories}
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
					<button class="btn rsync-makuo-button" id="svn-button" {if $app.svn_disabled}disabled{/if}>同期する{*SVN反映*}</button>
					<i class="icon-question-sign" data-original-title="svn export と mv を実行します。"></i>
				</div>
			</div>
			{/strip}
			</p>

            <p>
			{if $app.svn_disabled}
				※SVN反映は開発環境またはステージング環境で動作します。<br>
			{else}
				※実行後にバックアップディレクトリが残ります。これらは手動で削除して下さい。<br>
				　バックアップディレクトリ：{$smarty.const.BASE}/～.～.bak<br>
				<strong>※別途、デプロイ(makuo)を実行して下さい。</strong><br>
			{/if}
			</p>
			
            <div class="text-right">
                前回同期：
                <span id="svn-time">{if $app.last_svn}{$app.last_svn.time}{else}unknown{/if}</span>&nbsp;
                <span id="svn-user">{if $app.last_svn}{$app.last_svn.user}{/if}</span><br>
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
