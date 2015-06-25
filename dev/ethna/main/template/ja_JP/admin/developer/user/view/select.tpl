<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ閲覧 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<form action="list" method="post">
				{if $form.by == "id"}
				<h2>IDによる検索</h2>
					<font color="#ff0000">{$app.err_mes}<br /></font>
					ユーザーID：<input type="text" name="id"><br />
				{elseif $form.by == "name"}
				<h2>ニックネームによる検索</h2>
					<font color="#ff0000">{$app.err_mes}<br /></font>
					ニックネーム：<input type="text" name="nickname"><br />
				{elseif $form.by == "term"}
				<h2>指定期間による検索</h2>
					<font color="#ff0000">{$app.err_mes}<br /></font>
					対象開始日時：
					<input type="text" name="date_term_start" value="{$app.date_term_start}" class="jquery-ui-datetimepicker">
					～
					<input type="text" name="date_term_end" value="{$app.date_term_end}" class="jquery-ui-datetimepicker">
					<br />
				{/if}

				<input type="hidden" name="by" value="{$form.by}" />
				<input type="submit" value="実行" />
			</form>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
</body>
</html>