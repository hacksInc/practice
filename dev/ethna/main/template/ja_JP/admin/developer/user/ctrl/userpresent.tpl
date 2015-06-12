<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ制御 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<div class="page-header"><h2>ユーザプレゼントBOX確認</h2></div>

			<table cellpadding="6">
				<tr><th align="left">ユーザID</th><td>{$app.base.pp_id}</td></tr>
				<tr><th align="left">ニックネーム</th><td>{$app.base.name}</td></tr>
			</table>

			<h3>プレゼントBOX一覧</h3>
			<table border="0" class="table table-striped">
			<thead>
				<tr>
					<th><strong>No</strong></th>
					<th><strong>プレゼントID</strong></th>
					<th><strong>配布管理ID</strong></th>
					<th><strong>コメントID</strong></th>
					<th><strong>配布物カテゴリ</strong></th>
					<th><strong>アイテム名</strong></th>
					<th><strong>所持数</strong></th>
					<th><strong>状態</strong></th>
					<th><strong>データ作成日時</strong></th>
					<th><strong>データ更新日時</strong></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$app.item key=k item=v}
				<tr>
					<td>{$k+1}</td>
					<td>{$v.present_id}</td>
					<td>{$v.present_mng_id}</td>
					<td>{$v.comment_id}</td>
					<td>{$v.present_category}</td>
					<td>{$v._present_value}</td>
					<td>{$v.num}</td>
					<td>{$v.status_name}</td>
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
