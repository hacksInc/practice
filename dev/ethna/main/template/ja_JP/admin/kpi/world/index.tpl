<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ワールド集計 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>ワールド集計</h2>
			メニューを選択して下さい。<br />
			<ol>
				<li>{a href="item/select"}総アイテム数{/a}</li>
				<li>{a href="monster/select"}モンスター流通数{/a}</li>
				<li>{a href="quest/select"}クエスト分布{/a}</li>
				<li>{a href="rank/select"}ランク帯分布{/a}</li>
			</ol>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>