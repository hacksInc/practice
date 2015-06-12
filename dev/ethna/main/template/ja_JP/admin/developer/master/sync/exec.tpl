<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="`$app.table_label` - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

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
{*
※開発中のため、まだ実際には更新は行われていません。<br>
*}

			<p>
				{a href="../list?table=`$form.table`"}戻る{/a}
			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>