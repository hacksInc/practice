<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ランキング - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>賞品配布設定&nbsp;削除</h2>
			<p>
				削除しました。<br>
			</p>
			<p>
				<a href="../index?ranking_id={$app.ranking_id}">一覧へ戻る</a>
			</p>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>