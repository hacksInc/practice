<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="クエスト分布 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}
	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>クエスト分布</h2>
			日付：{$form.date}<br />
			※ランク午前2時付近、ランク以外は午前0時の値です。<br />
			ARPPU：{$app.arppu_range_name}<br />
			プラットフォーム：{$app.platform_name}<br />
			<table border="1">
			<thead>
				<tr>
					<th class="num">達成ノーマルクエスト数</th>
					<th class="num">人数</th>
					<th class="case">プレイヤーランク（平均）</th>
					<th class="num">脱落数</th>
					<th class="num">使用合成素材数（平均）</th>
				</tr>
			</thead>

			<tbody>
				{foreach from=$app.list key="k" item="row"}
				<tr>
					<td align="right">{$row.clear_num}</td>
					<td align="right">{$row.user_num}</td>
					<td align="right">{$row.rank_avg}</td>
					<td align="right">{$row.escape_num}</td>
					<td align="right">{$row.synthesis_material_avg}</td>
				</tr>
				{/foreach}
			</tbody>
			</table>
			<br />
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>