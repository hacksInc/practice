<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ガチャレアリティ操作ログ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ガチャレアリティ操作ログ</h2>
			<div class="row-fluid">
				<div class="span2">
					{form_name name="gacha_id"}
				</div>
				<div class="span2">
					{$form.gacha_id}
				</div>
			</div>
				
			<table class="table">
				<tr>
					<th rowspan="2">日時	</th>
					<th rowspan="2">アカウント</th>
					<th colspan="4">操作内容</th>
				</tr>
				<tr>
					<th>Action</th>
					<th>{$app.form_template.gacha_id.name}</th>
					<th>{$app.form_template.rarity.name}</th>
					<th>{$app.form_template.weight_float.name}</th>
				</tr>
				
				{foreach from=$app.list item="row"}
				<tr>
					<td>{$row.time}</td>
					<td>{$row.user}</td>
					<td>{$row.action_type}</td>
					<td>{$row.gacha_id|default:"&nbsp;"}</td>
					<td>{$row.rarity|default:"&nbsp;"}</td>
					<td>{$row.weight_float|default:"&nbsp;"}</td>
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