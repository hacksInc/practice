<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="テーブル選択 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">

		<div class="span9">
			<h2>テーブル選択</h2>
			<table class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th>テーブル名称</th>
						<th>データ更新日時</th>
						<th>更新者</th>
						<th>概要</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$app.list key="table" item="row"}
					<tr>
						<td>{a href="list?table=`$table`"}{$row.table_label}{/a}</td>
						<td>{$row.last_modified.date_modified}</td>
						<td>{$row.last_modified.account_upd}</td>
						<td>{$row.summary}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
