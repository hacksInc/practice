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
			<h2>マスターデータ&nbsp;	バックアップ</h2>

			<form action="exec" method="post">
				<label style="display:inline-block;">
					<input type="radio" name="kind" value="data" checked>データ
				</label>
				<br />
				<label style="display:inline-block;">
					<input type="radio" name="kind" value="schema">スキーマ
				</label>
				<div class="text-left">
				   <input type="submit" value="ファイル取得" class="btn" />
			   </div>
			</form>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>