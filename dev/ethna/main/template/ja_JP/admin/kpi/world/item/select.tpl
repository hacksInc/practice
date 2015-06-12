<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="総アイテム数 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<form action="view" method="post">
				<h2>総アイテム数</h2>
				日付(YYYY-MM-DD)<br />
				<input type="text" name="date" class="datepicker" value="{$app.date_default}"><br />

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