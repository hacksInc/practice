<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="購入履歴 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<form action="view" method="post">
				<h2>ID単位での購入履歴</h2>
				ユーザーID：<input type="text" name="id"><br />

				<input type="radio" name="format" value="html" checked>表示する</input><br />
				<input type="radio" name="format" value="csv">CSVでダウンロードする</input><br />

				<input type="submit" value="実行" />
			</form>
		</div><!--/span-->
	</div><!--/row-->
	
	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>