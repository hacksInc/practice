<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ガチャ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>おまけガチャ&nbsp;レアリティ</h2>
			<p>
				削除しました。<br>
			</p>
			<p>
				{a href="../../index?gacha_id=`$form.gacha_id`"}一覧へ戻る{/a}
			</p>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>