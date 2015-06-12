<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ヘルプメッセージ操作ログ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ヘルプメッセージ操作ログ</h2>
			<table class="table">
				<tr>
					<th rowspan="2">日時	</th>
					<th rowspan="2">アカウント</th>
					<th colspan="13">操作内容</th>
				</tr>
				<tr>
					<th>Action</th>
					<th>help_id</th>
					<th>{$app.form_template.use_name.name}</th>
					<th>{$app.form_template.message.name}</th>
				</tr>
				
				{foreach from=$app.list item="row"}
				<tr>
					<td>{$row.time}</td>
					<td>{$row.user}</td>
					<td>{$row.action_type}</td>
					<td>{$row.help_id|default:"&nbsp;"}</td>
					<td>{$row.use_name|default:"&nbsp;"}</td>
					<td>{$row.message|default:"&nbsp;"}</td>
				</tr>
				{/foreach}
			</table>
			<div class="text-right">
				{a href="/psychopass_game/admin/announce/message/help/index"}一覧へ戻る{/a}
			</div>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
