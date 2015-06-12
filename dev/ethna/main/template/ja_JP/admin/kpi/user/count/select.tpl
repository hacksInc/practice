<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="user create数 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<form action="view" method="post">
				<h2>user create数</h2>
				<p class="text-warning">
					<i class="icon-warning-sign"></i> リアルタイムで集計を行ないます。
				</p>
				生成日時<i class="icon-question-sign" data-original-title="下記の日時以上～未満で集計します。"></i><br />
				<input type="text" name="date_created_from" value="{*{$smarty.server.REQUEST_TIME|date_format:"%Y-%m-%d %H:%M:%S"}*}" class="jquery-ui-datetimepicker">
				～
				<input type="text" name="date_created_to" value="{*{$smarty.server.REQUEST_TIME|date_format:"%Y-%m-%d %H:%M:%S"}*}" class="jquery-ui-datetimepicker"><br />

				<input type="submit" value="集計実行" />
			</form>
		</div><!--/span-->
	</div><!--/row-->
	
	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
</body>
</html>