<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="マスターデータデプロイ確認 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

		<div class="span9">
			<h2>{$app.table_label}</h2>
			<h3>{if $form.mode == "deploy"}
				デプロイ
			{elseif $form.mode == "refresh"}
				リフレッシュ
			{elseif $form.mode == "standby"}
				スタンバイへ反映
			{/if}</h3>

			実行しました。<br />

			<p>
				{a href="index"}戻る{/a}
			</p>
		</div><!--/span-->
	</div><!--/row-->


	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
