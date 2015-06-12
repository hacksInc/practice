<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ヘルプ詳細文操作ログ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ヘルプ詳細文操作ログ</h2>
			<table class="table">
				<tr>
					<th rowspan="2">日時	</th>
					<th rowspan="2">アカウント</th>
					<th colspan="13">操作内容</th>
				</tr>
				<tr>
					<th>Action</th>
					<th>help_id</th>
					<th>{$app.form_template.priority.name}</th>
					<th>{$app.form_template.category_id.name}</th>
					<th>{$app.form_template.title.name}</th>
					<th>{$app.form_template.body.name}</th>
				</tr>

				{foreach from=$app.list item="row"}
				<tr>
					<td>{$row.time}</td>
					<td>{$row.user}</td>
					<td>{$row.action_type}</td>
					<td>{$row.help_id|default:"&nbsp;"}</td>
					<td>{$app.form_template.priority.option[$row.priority]|default:"&nbsp;"}</td>
					<td>{$app.category_list[$row.category_id]|default:"&nbsp;"}</td>
					<td>{$row.title|default:"&nbsp;"}</td>
					<td>{$row.body|default:"&nbsp;"}</td>
				</tr>
				{/foreach}
			</table>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
