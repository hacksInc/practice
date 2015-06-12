<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="`$app.sync_label[$form.mode].mode` - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>マスターデータ&nbsp;	{$app.sync_label[$form.mode].mode}</h2>

			実行しましたがエラーが発生しました。<br />
			msg:{$app.err_msg}<br />
			table:{$app.err_table}<br />
			sql:{$app.err_sql}<br />
			param:<br />
			{foreach from=$app.err_param key="key" item="label"}
				(key){$key}=(val){$label}<br />
			{/foreach}
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>