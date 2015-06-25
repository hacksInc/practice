<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="t_ranking_prize - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}
		<div class="span9">
			<h2>賞品配布設定アップロード 完了</h2>
			<br>
			賞品配布設定を追加しました。<br />
			<br>
			<p><a href="../index?ranking_id={$app.ranking_id}">賞品配布設定に戻る</a></p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>