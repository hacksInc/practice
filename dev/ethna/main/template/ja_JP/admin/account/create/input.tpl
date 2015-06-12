<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="アカウント追加 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>アカウント追加</h2>

			<form action="exec" method="post" class="form-horizontal">
			    <div class="control-group">
				    <label class="control-label" for="inputId">ID</label>
				    <div class="controls">
						<input type="text" name="lid" id="inputId" placeholder="cave.netアカウントに準じたもの">
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="inputPassword">Password</label>
				    <div class="controls">
						<input type="password" name="lpw" id="inputPassword" placeholder="任意の英数文字">
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">権限レベル</label>
				    <div class="controls">
						<select name="role">
							{html_options options=$app.role_master}
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