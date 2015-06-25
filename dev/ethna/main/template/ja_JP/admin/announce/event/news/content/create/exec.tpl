<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="イベントのお知らせ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>イベントのお知らせデータ登録&nbsp;完了</h2>
			<p>
				登録しました。
 			</p>
			{if $form.banner_uploaded}
		    <p class="text-warning">
			    <i class="icon-warning-sign"></i> 画像ファイルはデプロイされていません。
		    </p>
			{/if}
			<p>
				{a href="../index"}一覧へ戻る{/a}
 			</p>
       </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>