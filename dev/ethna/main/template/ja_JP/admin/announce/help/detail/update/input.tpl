<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ヘルプ詳細文 - サイコパス管理ページ"}
<body>
<link href="/psychopass_game/css/admin/announce/help.css" rel="stylesheet">
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ヘルプ詳細文データ登録</h2>
		    <p>
				<i class="icon-info-sign"></i> 使用可能なタグは以下の通りです。<br>
				　　{"<p> </p> <br /> <strong> </strong> <span style=\"color: #～;\"> </span>"|escape}<br>
		    </p>
			<form action="confirm" method="post" enctype="multipart/form-data" class="form-horizontal">
				<input type="hidden" name="help_id" value="{$app.row.help_id}">
			    <div class="row-fluid">
					<div class="span4">
						{form_name name="priority"}
						{form_input name="priority" default=$app.row.priority}
					</div>
					<div class="span4">
						{form_name name="category_id"}
						{form_input name="category_id" default=$app.row.category_id}
					</div>
				</div>

				<br>
				<div>
					{form_name name="title"}<br>
				</div>
				<div>
					<input type="text" name="title" class="tinymce" value="{$app.row.title}">
				</div>

				<div>
					{form_name name="body"}<br>
				</div>
				<div class="admin-announce-help-tinymce-body">
					 <textarea name="body" class="tinymce">{$app.row.body}</textarea>
				</div>
				<div class="admin-announce-help-tinymce-body-dummy">
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						修正{form_name name="picture"}
					</div>
					<div class="span10">
						<input type="file" name="picture" class="file-drop" accept="image/png"><i class="icon-question-sign" data-original-title="ファイルはドラッグ＆ドロップもできます。"></i>
					</div>
				</div>

				<div style="text-align:center;">
					{if $app.row.picture!=null}
						<img src="../image?img_id={$app.row.help_id}&type=picture&dummy={$app.mtime}">
					{/if}
				</div>

				<br>
				<div class="text-center">
				   <input type="submit" value="修正確認" class="btn" />
			   </div>
			</form>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
<script src="/psychopass_game/js/tinymce/tinymce.min.js"></script>
<script src="/psychopass_game/js/admin/announce/help.js"></script>
</body>
</html>
