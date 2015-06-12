<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザーIDの検索 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<form action="view" method="post">
				{if $form.by == "id"}
				<h2>IDによる検索</h2>
					ユーザーID：<input type="text" name="id"><br />
				{elseif $form.by == "name"}
				<h2>キャラクター名による検索</h2>
					ニックネーム：<input type="text" name="nickname"><br />
				{/if}

				<input type="hidden" name="by" value="{$form.by}" />
				<input type="submit" value="実行" />
			</form>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>