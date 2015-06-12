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
			{if $form.duration_type == 1}
			<h2>時別</h2>
			{elseif $form.duration_type == 2}
			<h2>日別</h2>
			{elseif $form.duration_type == 3}
			<h2>月別</h2>
			{/if}
			集計対象期間：{$form.start} ～ {$form.end}<br />

			<br />
			<table border="1">
				<tr>
					<th>日付</th>
					<th>プラットフォーム</th>
					<th>起動数</th>
				</tr>
				{foreach from=$app.list key="k" item="row"}
				<tr>
					<td>{$row.date_period}</td>
					<td>{$row.platform}</td>
					<td align="right">{$row.num}</td>
				</tr>
				{/foreach}
			</table>
			
			※起動数は、ユニークユーザでのカウント
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
