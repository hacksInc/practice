<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="アナウンスデータデプロイ制御操作ログ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>アナウンスデータデプロイ制御操作ログ</h2>
			<table class="table">
				<tr>
					<th>日時	</th>
					<th>アカウント</th>
					<th>操作内容</th>
				</tr>
				
				{foreach from=$app.list item="row"}
				<tr>
					<td>{$row.time}</td>
					<td>{$row.user}</td>
					<td>{$row.action_type}</td>
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