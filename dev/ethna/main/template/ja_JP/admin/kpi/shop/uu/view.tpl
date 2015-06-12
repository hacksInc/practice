<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="購入者数 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}
	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			{if $form.duration_type == 2}
			<h2>日別個別売上</h2>
			{elseif $form.duration_type == 3}
			<h2>月別個別売上</h2>
			{/if}
{*
			集計実行日時：{$smarty.server.REQUEST_TIME|date_format:"%Y-%m-%d %H:%M:%S"}<br />
*}
			集計対象期間：{$form.start} ～ {$form.end}<br />

			<table border="1">
				<tr>
					<th>{$app.date_name}</th><th>プラットフォーム</th><th>ショップID</th><th>アイテムID</th><th>アイテム名</th><th>個数</th><th>価格</th><th>UU</th>
				</tr>
				{foreach from=$app.list key="k" item="row"}
				<tr>
					<td>{$row.date_use_formatted}</td><td>{$row.platform}</td><td align="right">{$row.shop_id}</td><td align="right">{$row.item_id}</td><td>{$app.m_item[$row.item_id].name_ja}</td><td align="right">{$row.num}</td><td align="right">{$row.price}</td><td align="right">{$row.kpi_value}</td>
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
