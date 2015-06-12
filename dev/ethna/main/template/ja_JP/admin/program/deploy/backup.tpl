<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="デプロイ制御 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>プログラムデプロイ制御</h2>
			<h3>バックアップ</h3>
			対象ディレクトリ：{" "|implode:$app.directories}
			<p>
			<div class="row-fluid">
				<div class="span3">
					<form action="/psychopass_game/admin/api/tar/download" method="post" id="download-form">
						<input type="hidden" name="target" value="{$app.target}">
						<input type="submit" class="btn" value="ダウンロード" id="download-btn"><i class="icon-question-sign" data-original-title="対象ディレクトリをローカルPCへダウンロードできます。"></i>
					</form>
				</div>
			</div>
			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{include file="admin/_part/tar_download_js.tpl"}
</body>
</html>
