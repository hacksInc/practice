<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="エントリポイント設定 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>エントリポイント設定</h2>
			
			<p>
				以下のバージョン振り分け設定に変更します。問題なければ「変更」ボタンを押して下さい。<br>
			</p>

			<form action="exec" method="post" class="form-horizontal">
			    <div class="control-group">
				    <label class="control-label">
						{form_name name="current_ver"}
					</label>
				    <div class="controls">
						{$form.current_ver}
						<input type="hidden" name="current_ver" value="{$form.current_ver}">
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">
						{form_name name="review_ver"}
					</label>
				    <div class="controls">
						{$form.review_ver}
						<input type="hidden" name="review_ver" value="{$form.review_ver}">
				    </div>
			    </div>
			    <div class="control-group">
				    <div class="controls">
					   <input type="submit" value="変更" class="btn" />
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
