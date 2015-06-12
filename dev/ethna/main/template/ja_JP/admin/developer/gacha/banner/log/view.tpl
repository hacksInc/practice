<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ガチャマスター操作ログ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ガチャマスター操作ログ</h2>
			<small>
			<table class="table table-condensed">
				<tr>
					<th rowspan="2">日時	</th>
					<th rowspan="2">アカウント</th>
					<th colspan="10">操作内容</th>
				</tr>
				<tr>
					<th>Action</th>
					<th>{$app.form_template.ua.name}</th>
					<th>{$app.form_template.gacha_id.name}</th>
					<th nowrap>{$app.form_template.type.name}</th>
					<th>{$app.form_template.price.name}</th>
					<th>{$app.form_template.comment.name}</th>
					<th>{$app.form_template.sort_list.name}</th>
					<th nowrap>{$app.form_template.banner_image.name}</th>
					<th nowrap>{$app.form_template.banner_type.name}</th>
					<th>{$app.form_template.banner_url.name}</th>
					<th>{$app.form_template.width.name}</th>
					<th>{$app.form_template.height.name}</th>
					<th>{$app.form_template.position_x.name}</th>
					<th>{$app.form_template.position_y.name}</th>
					<th>{$app.form_template.date_start.name}</th>
					<th>{$app.form_template.date_end.name}</th>
					<th nowrap>{$app.form_template.disp_sts.name}</th>
				</tr>
				
				{foreach from=$app.list item="row"}
				<tr>
					<td>{$row.time}</td>
					<td>{$row.user}</td>
					<td>{$row.action_type}</td>
					<td>{$app.form_template.ua.option[$row.ua]|default:"&nbsp;"}</td>
					<td>{$row.gacha_id|default:"&nbsp;"}</td>
					<td>{$app.form_template.type.option[$row.type]|default:"&nbsp;"}</td>
					<td>{$row.price|default:"&nbsp;"}</td>
					<td>{$row.comment|default:"&nbsp;"}</td>
					<td>{$row.sort_list|default:"&nbsp;"}</td>
					<td>{if $row.banner_data}<img src="{$row.banner_data}" width="100">{else}&nbsp;{/if}</td>
					<td>{$app.form_template.banner_type.option[$row.banner_type]|default:"&nbsp;"}</td>
					<td>{$row.banner_url|default:"&nbsp;"}</td>
					<td>{$row.width|default:"&nbsp;"}</td>
					<td>{$row.height|default:"&nbsp;"}</td>
					<td>{$row.position_x|default:"&nbsp;"}</td>
					<td>{$row.position_y|default:"&nbsp;"}</td>
					<td>{$row.date_start|default:"&nbsp;"}</td>
					<td>{$row.date_end|default:"&nbsp;"}</td>
					<td>{$row.disp_sts|default:"&nbsp;"}</td>
				</tr>
				{/foreach}
			</table>
			</small>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>