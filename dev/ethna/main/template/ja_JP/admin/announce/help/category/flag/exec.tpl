<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ヘルプ大項目 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ヘルプ大項目</h2>
			<p>
				{if $form.test_flag}
						表示テスト中にしました。
				{else}
						表示テスト停止しました。
				{/if}
			</p>
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