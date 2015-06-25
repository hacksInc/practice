<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="デプロイ制御 - サイコパス管理ページ"}
<body>
{literal}
<style type="text/css">
	div.inline-label-block label { 
		display: inline-block;
		margin-right: 10px;
	}
</style>
{/literal}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>プログラムデプロイ検証</h2>
			<p>
			{strip}
			<form action="dest" method="post">
				<div class="row-fluid">
					<div class="span10 inline-label-block">
                        {form_input name="dest_directories"}
						&nbsp;&nbsp;&nbsp;<label><input type="checkbox" id="check-dest-directories-all"> 全件チェック</label>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span3">
						<input type="submit" class="btn" value="商用比較" {if $app.dest_disabled}disabled{/if}>
                        <i class="icon-question-sign" data-original-title="ステージング環境管理サーバと商用環境管理サーバを比較します。ステージング環境で実行する機能です。"></i>
					</div>
				</div>
			</form>
			{/strip}
			</p>
            
			<hr>
			<p>
			{strip}
			<form action="makuo" method="post">
				<div class="row-fluid">
					<div class="span10 inline-label-block">
                        {form_input name="makuo_directories"}
						&nbsp;&nbsp;&nbsp;<label><input type="checkbox" id="check-makuo-directories-all"> 全件チェック</label>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span3">
						<input type="submit" class="btn" value="未デプロイ検出">
                        <i class="icon-question-sign" data-original-title="管理サーバとWebサーバを比較します。"></i>
					</div>
				</div>
			</form>
			{/strip}
			</p>
			
			<hr>
			<p>
			{strip}
			<form action="svn" method="post">
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
					<div class="span10 inline-label-block">
						{form_input name="svn_directories"}
						&nbsp;&nbsp;&nbsp;<label><input type="checkbox" id="check-svn-directories-all"> 全件チェック</label>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span3">
						<input type="submit" class="btn" value="SVN比較" {if $app.svn_disabled}disabled{/if}>
                        <i class="icon-question-sign" data-original-title="SVNレポジトリと管理サーバを比較します。"></i>
					</div>
				</div>
			</form>
			{/strip}
			</p>
			
			<hr>
			{if $app.svn_disabled}
				※SVN比較は開発環境またはステージング環境で動作します。<br>
{*				
			{else}
				※SVN比較は実行後にテンポラリディレクトリが残ります。テンポラリディレクトリは手動で削除して下さい。<br>
				　テンポラリディレクトリ　：{$smarty.const.BASE}/tmp/svn～<br>
*}
			{/if}
			{if $app.dest_disabled}
				※商用比較はステージング環境で動作します。<br>
			{/if}
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{literal}
<script>
	$('#check-makuo-directories-all').on('change', function() {
		$('input[name="makuo_directories[]"]').prop('checked', this.checked);
	});
    
	$('#check-svn-directories-all').on('change', function() {
		$('input[name="svn_directories[]"]').prop('checked', this.checked);
	});
    
	$('#check-dest-directories-all').on('change', function() {
		$('input[name="dest_directories[]"]').prop('checked', this.checked);
	});
</script>
{/literal}
</body>
</html>
