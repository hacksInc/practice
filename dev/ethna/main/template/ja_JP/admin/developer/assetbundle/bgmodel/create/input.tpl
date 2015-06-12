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
			<h2>アセットバンドル&nbsp;ゲーム背景&nbsp;アップロードメニュー</h2>
			
			<form action="confirm" method="post" enctype="multipart/form-data" class="form-horizontal" id="form1">
			    <div class="control-group">
				    <label class="control-label">{form_name name="asset_bundle_android"}</label>
				    <div class="controls">
						<input type="file" name="asset_bundle_android" class="file-drop">
					    <div class="text-error" style="display:none;"></div>
				    </div>
			    </div>
				<div class="control-group">
				    <label class="control-label">{form_name name="asset_bundle_iphone"}</label>
				    <div class="controls">
						<input type="file" name="asset_bundle_iphone" class="file-drop">
					    <div class="text-error" style="display:none;"></div>
				    </div>
			    </div>
				<div class="control-group">
				    <label class="control-label">{form_name name="asset_bundle_pc"}</label>
				    <div class="controls">
						<input type="file" name="asset_bundle_pc" class="file-drop">
					    <div class="text-error" style="display:none;"></div>
						<div><i class="icon-question-sign" data-original-title="ファイルはドラッグ＆ドロップもできます。"></i></div>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ID</label>
				    <div class="controls">
						（ファイルより取得）
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ディレクトリ</label>
				    <div class="controls" id="dispDir">
						（ファイルより取得）
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ファイルバージョン</label>
				    <div class="controls">
						（ファイルより取得）
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ファイル名</label>
				    <div class="controls">
						（ファイルより取得）
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="start_date"}</label>
				    <div class="controls">
						<input type="text" name="start_date" value="{$smarty.server.REQUEST_TIME|date_format:"%Y-%m-%d"} 00:00:00" class="jquery-ui-datetimepicker">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="end_date"}</label>
				    <div class="controls">
						<input type="text" name="end_date" value="9999-12-31 23:59:00" class="jquery-ui-datetimepicker">
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="active_flg"}</label>
				    <div class="controls">
						{form_input name="active_flg" default="1"}
				    </div>
			    </div>
			    <div class="control-group">
				    <div class="controls">
					   <input type="submit" value="アップロード確認" class="btn">
				    </div>
			    </div>
			</form>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
<script src="/js/admin/developer/assetbundle.js"></script>
</body>
</html>
