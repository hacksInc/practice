<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>ユーザデータ</h2>
			ユーザID：{$app.base.user_id}<br />
			名前：{$app.base.name}<br />
			
			<ul>
			{foreach from=$app.labels key="table" item="label"}
				<li>{a href="edit?id=`$app.base.user_id`&table=`$table`"}{$label}{/a}</li>
			{/foreach}
			</ul>

		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
