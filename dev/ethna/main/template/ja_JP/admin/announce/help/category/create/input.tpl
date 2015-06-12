<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ヘルプ大項目 - サイコパス管理ページ"}
<body>
<link href="/psychopass_game/css/admin/announce/help.css" rel="stylesheet">
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>ヘルプ大項目データ登録</h2>
			<p>
			<i class="icon-info-sign"></i> 使用可能なタグは以下の通りです。<br>
				{"<p> </p> <br /> <strong> </strong> <span style=\"color: #～;\"> </span>"|escape}
			</p>
			<form action="confirm" method="post" enctype="multipart/form-data" class="form-horizontal">
				<div class="row-fluid">
					<div class="span6">
						{form_name name="priority"}
						{form_input name="priority" default=$app.priority|default:99}
					</div>
					<div class="span6">
						{*
						{form_name name="date_disp"}
						<input type="text" name="date_disp" value="{$smarty.server.REQUEST_TIME|date_format:"%Y-%m-%d %H:%M:%S"}" class="jquery-ui-datetimepicker">
						*}
					</div>
				</div>

				<br>
				<div>
					{form_name name="title"}<br>
				</div>
				<div>
					<input type="text" name="title" class="tinymce" value="{$app.row.title}">
				</div>

				<br>
				<div>
					{form_name name="date_start"}
					<input type="text" name="date_start" value="{$smarty.server.REQUEST_TIME|date_format:"%Y-%m-%d %H:%M:%S"}" class="admin-announce-help-datetimepicker">
				</div>

				<div>
					{form_name name="date_end"}
					<input type="text" name="date_end" value="{$app.date_end}" class="admin-announce-help-datetimepicker">
				</div>

				<br>
				<div class="text-center">
					<input type="submit" value="登録確認" class="btn" />
				</div>
			</form>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
<script src="/psychopass_game/js/tinymce/tinymce.min.js"></script>
<script src="/psychopass_game/js/admin/announce/help.js"></script>
</body>
</html>
