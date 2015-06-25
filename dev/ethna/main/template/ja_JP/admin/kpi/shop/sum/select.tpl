<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="販売個数 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<form action="view" method="post">
				{if $form.duration_type == 2}
				<h2>日別個別売上</h2>
					期間(YYYY-MM-DD)<br />
					<input type="text" name="start" class="datepicker" value="{$app.start_default}"> ～ <input type="text" name="end" class="datepicker" value="{$app.end_default}"><br />
				{elseif $form.duration_type == 3}
				<h2>月別個別売上</h2>
					期間(YYYY-MM)<br />
					<input type="text" name="start" class="monthpicker" value="{$app.start_default}"> ～ <input type="text" name="end" class="monthpicker" value="{$app.end_default}"><br />
				{/if}

				<input type="radio" name="format" value="html" checked>表示する</input><br />
				<input type="radio" name="format" value="csv">CSVでダウンロードする</input><br />
				<br />
				<input type="radio" name="platform_query" value="separate" checked>プラットフォームごとの値を取得する</input><br />
				<input type="radio" name="platform_query" value="mix">プラットフォーム問わない値を取得する</input><br />
				<br />
				<input type="hidden" name="duration_type" value="{$form.duration_type}" />
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