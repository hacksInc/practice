<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="パスワード変更 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{*{include file="admin/common/breadcrumb.tpl"}*}

	<div class="row-fluid">
		{*{include file="admin/common/sidebar.tpl"}*}

        <div class="span9">
			<h2>パスワード変更</h2>
			
			<form action="exec" method="post" class="form-horizontal">
			    <div class="control-group">
				    <label class="control-label">ID</label>
				    <div class="controls">
						{$smarty.session.lid}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="inputPassword">現在のPassword</label>
				    <div class="controls">
						<input type="password" name="lpw" id="inputPassword">
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="inputNew">新しいPassword</label>
				    <div class="controls">
						<input type="password" name="new" id="inputNew">
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="inputVerify">
						新しいPassword（確認）
						<i class="icon-question-sign" data-original-title="確認のため新しいパスワードをもう一度入力してください。"></i>
					</label>
				    <div class="controls">
						<input type="password" name="verify" id="inputVerify">
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