<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="`$app.sync_label[$form.mode].mode`操作ログ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>{$app.sync_label[$form.mode].mode}操作ログ<i class="icon-question-sign" data-original-title="新しい順に最大{$app.limit}件を表示します。"></i></h2>
			<table class="table">
				<tr>
					<th>日時	</th>
					<th>アカウント</th>
					<th>テーブル名称</th>
				</tr>
				
				{foreach from=$app.list item="row"}
				<tr>
					<td>{$row.date_created}</td>
					<td>{$row.account_reg}</td>
					<td>{$row.table_label|default:$row.table_name}
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