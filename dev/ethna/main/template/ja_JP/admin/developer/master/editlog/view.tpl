<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="`$app.table_label` - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">

        <div class="span9">
			<h2>{$app.table_label}</h2>
			<h3>直接編集ログ</h3>
			<p>
			新しい順に{$app.limit}件まで表示します。
			</p>
			<p>
			<table class="table">
				<tr>
					<th rowspan="2">日時	</th>
					<th rowspan="2">アカウント</th>
					<th rowspan="2">操作</th>
					<th rowspan="2">対象</th>
					<th rowspan="2">結果</th>
					<th colspan="{$app.labels_cnt}">データ</th>
				</tr>
				<tr>
					{foreach from=$app.labels item="label"}
						<th>{$label}</th>
					{/foreach}
				</tr>

				{foreach from=$app.list item="row"}
				<tr>
					<td>{$row.time}</td>
					<td>{$row.user}</td>
					<td>{$row.method_label}</td>
					<td>{$row.id|default:"&nbsp;"}</td>
					<td>{$row.http_status_label}</td>
					{foreach from=$app.labels item="label"}
						<td>{$row.$label|default:"&nbsp;"}</td>
					{/foreach}
				</tr>
				{/foreach}
			</table>
			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
