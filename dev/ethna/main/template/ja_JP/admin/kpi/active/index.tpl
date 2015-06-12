<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="アクティブ情報 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>アクティブ情報</h2>
			メニューを選択して下さい。<br />
			<ol>
				<li>{a href="period/index"}時間帯による起動数{/a}</li>
				<li>クエスト進捗による離脱率</li>
			</ol>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
