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
			<h2>ガチャ情報削除</h2>
			<div class="row-fluid">
				<div class="span2">
					ガチャID：
				</div>
				<div class="span4">
					{$app.gacha_id}
				</div>
			</div>

			<p>
				ガチャリスト管理情報<br>
				一次抽選情報<br>
				二次抽選情報<br>
				おまけガチャ管理情報<br>
				おまけガチャ一次抽選情報<br>
				おまけガチャ二次抽選情報<br>
			</p>
			<p>
				をクリアしました。
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