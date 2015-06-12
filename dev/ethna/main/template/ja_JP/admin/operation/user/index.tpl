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
			<h2>ユーザーIDの検索</h2>
			メニューを選択してください。
			<ol>
				<li>{a href="select?by=id"}IDによる検索{/a}</li>
				<li>{a href="select?by=name"}キャラクター名による検索{/a}</li>
			</ol>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
