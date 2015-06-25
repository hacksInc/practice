<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ログのダウンロード - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<form action="view" method="post">
				期間(YYYY-MM-DD)<br />
				<input type="text" name="start" class="datepicker"> ～ <input type="text" name="end" class="datepicker"><br />

				ユーザID<br />
				<input type="text" name="user_id"><br />
				
{*
				<input type="radio" name="format" value="html" checked>表示する</input><br />
*}
				<input type="radio" name="format" value="csv" checked>CSVでダウンロードする</input><br />

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