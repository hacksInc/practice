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
			<div class="page-header"><h2>ニックネームによる検索</h2></div>

			<table border="0" class="table table-striped">
			<thead>
				<tr>
					<th><strong>ニックネーム</strong></th>
					<th><strong>ユーザID</strong></th>
					<th><strong>OS</strong></th>
					<th><strong>最終ログイン</strong></th>
					<th><strong>登録日</strong></tdth
				</tr>
			</thead>
			<tbody>
			{foreach from=$app.bases item="base"}
				<tr>
					<td>{a href="list?by=id&id=`$base.pp_id`"}{$base.name}{/a}</td>
					<td>{$base.pp_id}</td>
					<td>{if $base.device_type==1}iOS{elseif $base.device_type==2}Android{/if}</td>
					<td>{$base.last_login}</td>
					<td>{$base.date_created}</td>
				</tr>
			{/foreach}
			</tbody>
			</table>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>