<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="購入履歴 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}
	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>ID単位での購入履歴</h2>
{*
			集計実行日時：{$smarty.server.REQUEST_TIME|date_format:"%Y-%m-%d %H:%M:%S"}<br />
*}
			ユーザID：{$app.base.user_id}<br />
			名前：{$app.base.name}<br />

			<br />
			開始日から最終ログインまでの購入履歴
			<table border="1">
				<tr>
					<th>日付(年月)</th><th>購入アイテム名</th><th>プラットホーム名</th><th>num</th><th>price</th>
				</tr>
				{foreach from=$app.user_shop_list key="k" item="row"}
				<tr>
					<td>{$row.date_use}</td>
					<td>{$app.item_name_assoc[$row.item_id]}</td>
					<td>{$app.platform_display_name}</td>
					<td align="right">{$row.num}</td>
					<td align="right">{$row.price}</td>
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
