<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="モンスター画像操作ログ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>モンスター画像操作ログ</h2>
<!--
			<p>
				操作ログを
				<ul><li>日時</li><li>アカウント</li><li>操作内容</li></ul>
				で記録したものが閲覧できます。<br>
				最大200件。
			</p>
-->			
			<table class="table">
				<tr>
					<th rowspan="2">日時	</th>
					<th rowspan="2">アカウント</th>
					<th colspan="8">操作内容</th>
				</tr>
				<tr>
					<th>種別</th>
					<th>{$app.form_template.id.name}</th>
					<th>モンスターアイコン</th>
					<th>モンスター画像</th>
					<th>{$app.form_template.asset_bundle_android.name}</th>
					<th>{$app.form_template.asset_bundle_iphone.name}</th>
					<th>{$app.form_template.asset_bundle_pc.name}</th>
					<th>ファイル名</th>
					<th>ファイルバージョン</th>
					<th>開始日</th>
					<th>終了日</th>
					<th>活性フラグ</th>
				</tr>
				
				{foreach from=$app.list item="row"}
				<tr>
					<td>{$row.time}</td>
					<td>{$row.user}</td>
					<td>{$row.action_type}</td>
					<td>{$row.id|default:"&nbsp;"}</td>
					<td>{$row.monster_icon|default:"&nbsp;"}</td>
					<td>{$row.monster_image|default:"&nbsp;"}</td>
					<td>{$row.asset_bundle_android|default:"&nbsp;"}</td>
					<td>{$row.asset_bundle_iphone|default:"&nbsp;"}</td>
					<td>{$row.asset_bundle_pc|default:"&nbsp;"}</td>
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