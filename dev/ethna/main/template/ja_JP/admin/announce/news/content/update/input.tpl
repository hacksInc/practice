<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="お知らせ - サイコパス管理ページ"}
<body>
<link href="/psychopass_game/css/admin/announce/news.css" rel="stylesheet">
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>お知らせデータ登録</h2>
		    <p>
				<i class="icon-info-sign"></i> 使用可能なタグは以下の通りです。<br>
				　　{"<p> </p> <br /> <strong> </strong> <span style=\"color: #～;\"> </span>"|escape}<br>
		    </p>
			<form action="confirm" method="post" enctype="multipart/form-data" class="form-horizontal">
				<input type="hidden" name="content_id" value="{$app.row.content_id}">
			    <div class="row-fluid">
					<div class="span6">
						{form_name name="priority"}
						{form_input name="priority" default=$app.row.priority}
					</div>
					<div class="span6">
						{form_name name="date_disp"}
						<input type="text" name="date_disp" value="{$app.row.date_disp}" class="jquery-ui-datetimepicker">
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						修正{form_name name="banner"}
					</div>
					<div class="span10">
						<input type="file" name="banner" class="file-drop" accept="image/png"><i class="icon-question-sign" data-original-title="ファイルはドラッグ＆ドロップもできます。"></i>
					</div>
				</div>

				<div style="text-align:center;">
					{if $app.row.banner!=null}
						<img src="../image?img_id={$app.row.content_id}&type=banner&dummy={$app.mtime}">
					{/if}
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
						<img src="../image?img_id={$app.row.content_id}&type=picture&dummy={$app.mtime}">
					{/if}
				</div>

				<br>
				<div>
					{form_name name="title"}<br>
				</div>
				<div>
					<input type="text" name="title" class="tinymce" value="{$app.row.title}">
				</div>

				<div>
					{form_name name="abridge"}<br>
				</div>
				<div>
					<input type="text" name="abridge" class="tinymce" value="{$app.row.abridge}">
				</div>

				<div>
					{form_name name="body"}<br>
				</div>
				<div class="admin-announce-news-content-tinymce-body">
					 <textarea name="body" class="tinymce">{$app.row.body}</textarea>
				</div>
				<div class="admin-announce-news-content-tinymce-body-dummy">
				</div>

				<br>
				<div>
					{form_name name="date_start"}
					<input type="text" name="date_start" value="{$app.row.date_start}" class="admin-announce-news-content-datetimepicker">
				</div>

				<div>
					{form_name name="date_end"}
					<input type="text" name="date_end" value="{$app.row.date_end}" class="admin-announce-news-content-datetimepicker">
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
<script src="/psychopass_game/js/admin/announce/news.js"></script>
</body>
</html>
