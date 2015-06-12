<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="user create数 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}
	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>user create数</h2>
			<p>
				生成日時<br />
				{$form.date_created_from}
				～
				{$form.date_created_to}<br />
				<br />
				ユーザー数<br />
				{$app.cnt}<br />
				<br />
				（{$smarty.server.REQUEST_TIME|date_format:"%Y-%m-%d %H:%M"}現在）
 			</p>
			<p>
				{a href="select"}戻る{/a}
 			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
