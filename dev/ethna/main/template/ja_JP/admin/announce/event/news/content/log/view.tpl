<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="イベントのお知らせ操作ログ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>イベントのお知らせ操作ログ</h2>
			<table class="table">
				<tr>
					<th rowspan="2">日時	</th>
					<th rowspan="2">アカウント</th>
					<th colspan="11">操作内容</th>
				</tr>
				<tr>
					<th>Action</th>
					<th>content_id</th>
					<th>{$app.form_template.ua.name}</th>
					<th>{$app.form_template.priority.name}</th>
					<th>{$app.form_template.date_disp.name}</th>
					<th nowrap>{$app.form_template.banner_image.name}</th>
					<th>{$app.form_template.banner_disabled.name}</th>
					<th>{$app.form_template.body.name}</th>
					<th>{$app.form_template.date_start.name}</th>
					<th>{$app.form_template.date_end.name}</th>
					<th>{$app.form_template.disp_sts.name}</th>
				</tr>
				
				{foreach from=$app.list item="row"}
				<tr>
					<td>{$row.time}</td>
					<td>{$row.user}</td>
					<td>{$row.action_type}</td>
					<td>{$row.content_id|default:"&nbsp;"}</td>
					
					<td>{$app.form_template.ua.option[$row.ua]|default:"&nbsp;"}</td>
					<td>{$app.form_template.priority.option[$row.priority]|default:"&nbsp;"}</td>
					<td>{$row.date_disp|default:"&nbsp;"}</td>
					<td>{if $row.banner_data}<img src="{$row.banner_data}" width="100">{else}&nbsp;{/if}</td>
					<td>{$row.banner_disabled|default:"&nbsp;"}</td>
					<td>{$row.body|default:"&nbsp;"}</td>
					<td>{$row.date_start|default:"&nbsp;"}</td>
					<td>{$row.date_end|default:"&nbsp;"}</td>
					<td>{$row.disp_sts|default:"&nbsp;"}</td>
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