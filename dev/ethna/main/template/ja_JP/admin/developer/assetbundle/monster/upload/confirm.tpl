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
			<h2>アセットバンドル&nbsp;モンスター画像データ&nbsp;アップロード確認</h2>
			
			{if $app.row_exists}
		    <div class="alert">
			    <button type="button" class="close" data-dismiss="alert">&times;</button>
			    <i class="icon-warning-sign"></i> このモンスターIDはm_asset_bandleテーブルに既に登録されています。
		    </div>
			{/if}

			<form action="exec" method="post" class="form-horizontal">
				<input type="hidden" name="confirm_uniq" value="{$app.confirm_uniq}">
				
				<div class="control-group">
				    <label class="control-label">モンスターアイコン</label>
				    <div class="controls">
						{$app.monster_icon.name}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">モンスター画像</label>
				    <div class="controls">
						{$app.monster_image.name}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">アセットバンドルAndroid用</label>
				    <div class="controls">
						{$app.asset_bundle_android.name}
				    </div>
			    </div>
				<div class="control-group">
				    <label class="control-label">アセットバンドルiPhone用</label>
				    <div class="controls">
						{$app.asset_bundle_iphone.name}
				    </div>
			    </div>
				<div class="control-group">
				    <label class="control-label">アセットバンドルPC用</label>
				    <div class="controls">
						{$app.asset_bundle_pc.name}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ID</label>
				    <div class="controls">
						{$app.monster_id}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ディレクトリ</label>
				    <div class="controls">
						monster/{$app.monster_id}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">ヴァージョン</label>
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
				    <label class="control-label">開始日</label>
				    <div class="controls">
						{$form.start_date}
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">終了日</label>
				    <div class="controls">
						{$form.end_date}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">活性フラグ</label>
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