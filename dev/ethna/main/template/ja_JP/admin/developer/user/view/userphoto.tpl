<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ閲覧 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span6">
			<div class="page-header"><h2>ユーザ所持フォト確認</h2></div>

			<table cellpadding="6">
				<tr><th align="left">ユーザID</th><td>{$app.base.pp_id}</td></tr>
				<tr><th align="left">ニックネーム</th><td>{$app.base.name}</td></tr>
			</table>

			<h3>所持フォト一覧</h3>
			<table border="0" class="table table-striped">
			<thead>
				<tr>
					<th><strong>No</strong></th>
					<th><strong>フォトID</strong></th>
					<th><strong>PHOTO No</strong></th>
					<th><strong>フォトレベル</strong></th>
					<th><strong>データ作成日時</strong></th>
					<th><strong>データ更新日時</strong></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$app.item key=k item=v}
				<tr>
					<td>{$k+1}</td>
					<td>{$v.photo_id}</td>
					<td>{$v._photo_id}</td>
					<td>{$v.photo_lv}</td>
					<td>{$v.date_created}</td>
					<td>{$v.date_modified}</td>
				</tr>
				{/foreach}
			</tbody>
			</table>

			<p>
				<br />
				{a href="list?by=id&id=`$form.id`"}戻る{/a}
			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
