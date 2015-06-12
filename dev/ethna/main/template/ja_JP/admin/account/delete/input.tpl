<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="アカウント削除 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>アカウント削除</h2>

			<form action="confirm" method="post" class="form-horizontal">
			    <div class="control-group">
				    <label class="control-label" for="inputId">ID</label>
				    <div class="controls">
						<input type="text" name="lid" id="inputId"><br>
				    </div>
			    </div>
			    <div class="control-group">
				    <div class="controls">
					   <input type="submit" value="次へ" class="btn" />
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