<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザーIDの検索 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>キャラクター名による検索</h2>
			{foreach from=$app.bases item="base"}
				{a href="view?by=id&id=`$base.user_id`"}{$base.name}{/a}<br />
			{/foreach}
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>