<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ゲーム背景 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>アセットバンドル&nbsp;ゲーム背景&nbsp;アップロード確認</h2>

			<form action="exec" method="post" class="form-horizontal">
				<input type="hidden" name="confirm_uniq" value="{$app.confirm_uniq}">

			    <div class="control-group">
				    <label class="control-label">{form_name name="asset_bundle_android"}</label>
				    <div class="controls">
						{$app.asset_bundle_android.name}
				    </div>
			    </div>
				<div class="control-group">
				    <label class="control-label">{form_name name="asset_bundle_iphone"}</label>
				    <div class="controls">
						{$app.asset_bundle_iphone.name}
				    </div>
			    </div>
				<div class="control-group">
				    <label class="control-label">{form_name name="asset_bundle_pc"}</label>
				    <div class="controls">
						{$app.asset_bundle_pc.name}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ID</label>
				    <div class="controls">
						{$app.bgmodel_id}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ディレクトリ</label>
				    <div class="controls">
						{$app.dir}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ファイルバージョン</label>
				    <div class="controls">
						{$app.version}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ファイル名</label>
				    <div class="controls">
						{$app.file_name}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="start_date"}</label>
				    <div class="controls">
						{$form.start_date}
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="end_date"}</label>
				    <div class="controls">
						{$form.end_date}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="active_flg"}</label>
				    <div class="controls">
						{$form.active_flg}
				    </div>
			    </div>
			    <div class="control-group">
				    <div class="controls">
					   <input type="submit" value="アップロード" class="btn" />
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
