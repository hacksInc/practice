<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="アセットバンドル - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>アセットバンドル</h2>
			<ul>
				<li>{a href="monster/index"}モンスター画像{/a}</li>
				<li>{a href="effect/index"}エフェクトデータ{/a}</li>
				<li>{a href="bgmodel/index"}ゲーム背景{/a}</li>
				<li>{a href="sound/index"}サウンド{/a}</li>
				<li>{a href="map/index"}クエストマップ{/a}</li>
				<li>{a href="version/index"}リソースバージョン{/a}</li>
				<li>{a href="worldmap/index"}ワールドマップ{/a}</li>
				<li>{a href="others/index"}その他データ{/a}</li>
			</ul>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>