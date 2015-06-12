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
			<h2>イベントのお知らせ</h2>
			<p>
				{if $form.disp_sts == constant("Jm_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_NORMAL")}
					表示ステータスを「通常」にしました。
				{elseif $form.disp_sts == constant("Jm_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_TEST")}
					表示ステータスを「表示テスト」にしました。
				{elseif $form.disp_sts == constant("Jm_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_PAUSE")}
					表示ステータスを「表示一時停止」にしました。
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