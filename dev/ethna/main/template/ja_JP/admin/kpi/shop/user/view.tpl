<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="会員数 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}
	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			{if $form.duration_type == 2}
			<h2>日別会員数</h2>
			{elseif $form.duration_type == 3}
			<h2>月別会員数</h2>
			{/if}
{*
			集計実行日時：{$smarty.server.REQUEST_TIME|date_format:"%Y-%m-%d %H:%M:%S"}<br />
*}
			集計対象期間：{$form.start} ～ {$form.end}<br />

			<br />
			<table border="1">
				<tr>
					<th>日付</th>
					<th>プラットフォーム</th>
					<th>新規登録者数</th>
					<th>会員数</th>
					<th>{$app.active_name}</th>
					<th>{$app.active_name}率</th>
					<th>課金UU</th>
					<th>ARPU</th>
					<th>ARPPU</th>
				</tr>
				{foreach from=$app.list key="k" item="row"}
				<tr>
					<td>{$row.date_action}</td>
					<td>{$row.platform}</td>
					<td align="right">{$row.user_create_daily_num}</td>
					<td align="right">{$row.user_create_total_num}</td>
					<td align="right">{$row.active_user_num}</td>
					<td align="right">{$row.active_user_percentage}</td>
					<td align="right">{$row.payment_user_num}</td>
					<td align="right">{$row.arpu}</td>
					<td align="right">{$row.arppu}</td>
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
