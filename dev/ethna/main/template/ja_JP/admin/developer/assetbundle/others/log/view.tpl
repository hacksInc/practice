<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="その他データ操作ログ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>その他データ操作ログ</h2>
			<table class="table">
				<tr>
					<th rowspan="2">日時	</th>
					<th rowspan="2">アカウント</th>
					<th colspan="12">操作内容</th>
				</tr>
				<tr>
					<th>種別</th>
					<th>{$app.form_template.id.name}</th>
					<th>{$app.form_template.asset_bundle_android.name}</th>
					<th>{$app.form_template.asset_bundle_iphone.name}</th>
					<th>{$app.form_template.asset_bundle_pc.name}</th>
					<th>file_type</th>
					<th>ディレクトリ</th>
					<th>ファイル名</th>
					<th>ファイルバージョン</th>
					<th>{$app.form_template.start_date.name}</th>
					<th>{$app.form_template.end_date.name}</th>
					<th>{$app.form_template.active_flg.name}</th>
				</tr>
				
				{foreach from=$app.list item="row"}
				<tr>
					<td>{$row.time}</td>
					<td>{$row.user}</td>
					<td>{$row.action_type}</td>
					<td>{$row.id|default:"&nbsp;"}</td>
					<td>{$row.asset_bundle_android|default:"&nbsp;"}</td>
					<td>{$row.asset_bundle_iphone|default:"&nbsp;"}</td>
					<td>{$row.asset_bundle_pc|default:"&nbsp;"}</td>
					<td>{$row.file_type|default:"&nbsp;"}</td>
					<td>{$row.dir|default:"&nbsp;"}</td>
					<td>{$row.file_name|default:"&nbsp;"}</td>
					<td>{$row.version|default:"&nbsp;"}</td>
					<td>{$row.start_date|default:"&nbsp;"}</td>
					<td>{$row.end_date|default:"&nbsp;"}</td>
					<td>{$row.active_flg|default:"&nbsp;"}</td>
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