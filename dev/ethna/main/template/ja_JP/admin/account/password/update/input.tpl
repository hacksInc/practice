<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="パスワード変更 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>パスワード変更</h2>
			※閲覧は行なえません。
			
			<form action="exec" method="post" class="form-horizontal">
			    <div class="control-group">
				    <label class="control-label">ID</label>
				    <div class="controls">
						{$form.lid}
						<input type="hidden" name="lid" value="{$form.lid}"><br>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="inputPassword">Password</label>
				    <div class="controls">
						<input type="password" name="lpw" id="inputPassword">
				    </div>
			    </div>
			    <div class="control-group">
				    <div class="controls">
					   <input type="submit" value="実行" class="btn" />
				    </div>
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