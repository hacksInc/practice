<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="デプロイ制御 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>未デプロイ検出</h2>

			{foreach from=$app.lists item="list" key="dir"}
				{$dir}<br>
				{foreach from=$list item="line"}
					{$line}<br>
				{/foreach}
				<hr>
			{/foreach}
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
