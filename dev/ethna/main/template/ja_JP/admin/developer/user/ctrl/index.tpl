<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザ制御 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<div class="page-header"><h2>ユーザデータ制御</h2></div>

			<font color="#ff0000">{$app.err_mes}<br /></font>
			<form action="list" method="post">
				<h4>IDによる検索</h4>
					ユーザーID：<input type="text" name="id"><br />
				<input type="hidden" name="by" value="id" />
				<input type="submit" value="実行" />
			</form>
			<form action="list" method="post">
				<h4>ニックネームによる検索</h4>
					ニックネーム：<input type="text" name="nickname"><br />
				<input type="hidden" name="by" value="name" />
				<input type="submit" value="実行" />
			</form>
			<form action="list" method="post">
				<h4>指定期間による検索</h4>
					対象開始日時：
					<input type="text" name="date_term_start" value="{$app.date_term_start}" class="jquery-ui-datetimepicker">
					～
					<input type="text" name="date_term_end" value="{$app.date_term_end}" class="jquery-ui-datetimepicker">
					<br />
				<input type="hidden" name="by" value="term" />
				<input type="submit" value="実行" />
			</form>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
</body>
</html>
