<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="メインバナー操作ログ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>メインバナー操作ログ</h2>
			<small>
				<table class="table table-condensed">
					<tr>
						<th rowspan="2">日時	</th>
						<th rowspan="2">アカウント</th>
						<th colspan="18">操作内容</th>
					</tr>
					<tr>
						<th>Action</th>
						<th>{$app.form_template.hbanner_id.name}</th>
						<th>{$app.form_template.ua.name}</th>
						<th>{$app.form_template.pri.name}</th>
						<th>{$app.form_template.memo.name}</th>
						<th>{$app.form_template.url.name}</th>
						<th>{$app.form_template.banner_attribute.name}</th>
						<th nowrap>{$app.form_template.banner_image.name}</th>
						<th>{$app.form_template.date_start.name}</th>
						<th>{$app.form_template.date_end.name}</th>
						<th>{$app.form_template.disp_sts.name}</th>
					</tr>

					{foreach from=$app.list item="row"}
					<tr>
						<td>{$row.time}</td>
						<td>{$row.user}</td>
						<td>{$row.action_type}</td>
						<td>{$row.hbanner_id|default:"&nbsp;"}</td>
						<td>{$app.form_template.ua.option[$row.ua]|default:"&nbsp;"}</td>
						<td>{$app.form_template.pri.option[$row.pri]|default:"&nbsp;"}</td>
						<td>{$row.memo|default:"&nbsp;"}</td>
						<td>{$row.url|default:"&nbsp;"}</td>
						<td>{$row.banner_attribute|default:"&nbsp;"}</td>
						<td>{if $row.banner_data}<img src="{$row.banner_data}" width="100">{else}&nbsp;{/if}</td>
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
