<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="モンスター画像 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>アセットバンドル&nbsp;モンスター画像データ&nbsp;修正</h2>
			<form action="confirm" method="post" enctype="multipart/form-data" class="form-horizontal" id="form1">
				<input type="hidden" name="id" value="{$app.row.id}">

				<div class="control-group">
				    <label class="control-label">モンスターアイコン</label>
				    <div class="controls">
						<input type="file" name="monster_icon" class="file-drop" accept="image/png">
					    <div class="text-error" style="display:none;"></div>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">モンスター画像</label>
				    <div class="controls">
						<input type="file" name="monster_image" class="file-drop" accept="image/png">
					    <div class="text-error" style="display:none;"></div>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">アセットバンドルAndroid用</label>
				    <div class="controls">
						<input type="file" name="asset_bundle_android" class="file-drop">
					    <div class="text-error" style="display:none;"></div>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">アセットバンドルiPhone用</label>
				    <div class="controls">
						<input type="file" name="asset_bundle_iphone" class="file-drop">
					    <div class="text-error" style="display:none;"></div>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">アセットバンドルPC用</label>
				    <div class="controls">
						<input type="file" name="asset_bundle_pc" class="file-drop">
					    <div class="text-error" style="display:none;"></div>
						<div><i class="icon-question-sign" data-original-title="ファイルはドラッグ＆ドロップもできます。"></i></div>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ID</label>
				    <div class="controls">
						{$app.row.dir|basename}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ディレクトリ</label>
				    <div class="controls">
						{$app.row.dir}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ヴァージョン</label>
				    <div class="controls">
						{$app.row.version}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ファイル名</label>
				    <div class="controls">
						{$app.row.file_name}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">開始日</label>
				    <div class="controls">
						<input type="text" name="start_date" value="{$app.row.start_date}" class="jquery-ui-datetimepicker">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">終了日</label>
				    <div class="controls">
						<input type="text" name="end_date" value="{$app.row.end_date}" class="jquery-ui-datetimepicker">
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">活性フラグ</label>
				    <div class="controls">
						{form_input name="active_flg" default=$app.row.active_flg}
				    </div>
			    </div>
			    <div class="control-group">
				    <div class="controls">
					   <input type="submit" value="修正確認" class="btn" />
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