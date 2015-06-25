<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="イベントのお知らせ - サイコパス管理ページ"}
<body>
<link href="/css/admin/announce/event/news.css" rel="stylesheet">
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>イベントのお知らせデータ&nbsp;修正</h2>
		    <p>
				<i class="icon-info-sign"></i> 使用可能なタグは以下の通りです。<br>
				　　{"<p> </p> <br /> <strong> </strong> <span style=\"color: #～;\"> </span> <a ～> </a>"|escape}
		    </p>			
			<form action="confirm" method="post" enctype="multipart/form-data" class="form-horizontal">
				<input type="hidden" name="content_id" value="{$app.row.content_id}">
				

				<br>
			    <div class="row-fluid">
					<div class="span6">
					</div>
					<div class="span6">
					    <div class="row-fluid">
							<div class="span3">
								{form_name name="ua"}
							</div>
							<div class="span9">
								{form_input name="ua" default=$app.row.ua}
							</div>
						</div>
					</div>
				</div>

				<br>
			    <div class="row-fluid">
					<div class="span6">
						<div class="row-fluid">
							<div class="span3">
								{form_name name="priority"}
							</div>
							<div class="span9">
								{form_input name="priority" default=$app.row.priority}
							</div>
						</div>
					</div>
					<div class="span6">
						<div class="row-fluid">
							<div class="span3">
								{form_name name="date_disp"}
							</div>
							<div class="span9">
								<input type="text" name="date_disp" value="{$app.row.date_disp}" class="jquery-ui-datetimepicker">
							</div>
						</div>
					</div>
				</div>
				
				{if $app.row.banner}
				<br>
			    <div>
					<img src="../image?content_id={$app.row.content_id}">&nbsp;&nbsp;
					<label for="banner_disabled_1" style="display: inline-block;"><input type="checkbox" name="banner_disabled" value="1" id="banner_disabled_1" />バナー解除</label>
			    </div>
				{/if}

				<br>
				<div class="row-fluid">
					<div class="span9">
						<div class="row-fluid">
							<div class="span2">
								{form_name name="banner_image"}&nbsp;&nbsp;
						    </div>
							<div class="span10">
								<input type="file" name="banner_image" class="file-drop" accept="image/png"><i class="icon-question-sign" data-original-title="ファイルはドラッグ＆ドロップもできます。"></i>
						    </div>
					    </div>
				    </div>
			    </div>

				<br>
				<div class="admin-announce-event-news-content-tinymce-body">
					 <textarea name="body" class="tinymce">{$app_ne.body}</textarea>
				</div>
				<div class="admin-announce-event-news-content-tinymce-body-dummy">
				</div>
				
				<br>
				<div>
					{form_name name="date_start"}
					<input type="text" name="date_start" value="{$app.row.date_start}" class="admin-announce-event-news-content-datetimepicker">
				</div>
				
				<div>
					{form_name name="date_end"}
					<input type="text" name="date_end" value="{$app.row.date_end}" class="admin-announce-event-news-content-datetimepicker">
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
<script src="/js/tinymce/tinymce.min.js"></script>
<script src="/js/admin/announce/event/news.js"></script>
</body>
</html>