<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="権限変更 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>権限変更</h2>

			<form action="exec" method="post" class="form-horizontal">
			    <div class="control-group">
				    <label class="control-label" for="inputId">ID</label>
				    <div class="controls">
						{$app.user.user}
						<input type="hidden" name="lid" value="{$form.lid}"><br>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">権限レベル</label>
				    <div class="controls">
						<select name="role">
							{html_options options=$app.role_master selected=$app.user.role}
						</select>
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