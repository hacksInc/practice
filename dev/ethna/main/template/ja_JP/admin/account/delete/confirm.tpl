<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="アカウント削除 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>アカウント削除</h2>
			確認して下さい。<br><br>
			<form action="exec" method="post" class="form-horizontal">
				<input type="hidden" name="lid" value="{$form.lid}">
			    ID: {$form.lid}<br>
				<input type="submit" value="削除実行" class="btn" />
			</form>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>