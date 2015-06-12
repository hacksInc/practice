<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="会員数 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>会員数</h2>
			<ol>
				<li>{a href="select?duration_type=2"}日別{/a}</li>
				<li>{a href="select?duration_type=3"}月別{/a}</li>
			</ol>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
