<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="総アイテム数 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}
	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>総アイテム数</h2>
			日付：{$form.date}<br />
			※午前2時付近の値です。<br />

			<br />
			<table border="1">
			<thead>
				<tr>
					<th>アイテム名</th>
					<th class="num">アイテムID</th>
					<th class="case">プラットホーム名</th>
					<th class="num">流通数</th>
				</tr>
			</thead>

			<tbody>
				{foreach from=$app.list key="k" item="row"}
				<tr>
					<td>{$row.name}</td>
					<td align="right">{$row.item_id}</td>
					<td>{$row.platform}</td>
					<td align="right">{$row.sum_num}</td>
				</tr>
				{/foreach}
			</tbody>
			</table>
			<br />
			※流通数とは、ユーザの所持数の合計。
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
