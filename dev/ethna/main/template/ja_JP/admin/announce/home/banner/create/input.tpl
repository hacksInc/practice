<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="メインバナー - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>メインバナー登録</h2>
			<form action="confirm" method="post" enctype="multipart/form-data" class="form-horizontal">
				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="hbanner_id"}
					</div>
					<div class="span4">
						（登録時に付与）
					</div>
					<div class="span2">
						{form_name name="ua"}
					</div>
					<div class="span4">
						{form_input name="ua" default=$app.row.ua}
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="img_id"}
					</div>
					<div class="span4">
						（登録時に付与）
					</div>
					<div class="span2">
						{form_name name="type"}
					</div>
					<div class="span4">
						{form_input name="type" default=$app.row.type}
					</div>
				</div>
				
				<br>
				<div class="row-fluid">
					<div class="span6">
						&nbsp;
					</div>
					<div class="span2">
						{form_name name="pri"}
					</div>
					<div class="span4">
						{form_input name="pri" default=$app.row.pri}
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="memo"}
					</div>
					<div class="span10">
						{form_input name="memo" class="span12"}
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="banner_image"}
					</div>
					<div class="span10">
						<input type="file" name="banner_image" class="file-drop" accept="image/png"><i class="icon-question-sign" data-original-title="ファイルはドラッグ＆ドロップもできます。"></i>
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="url_ja"}
					</div>
					<div class="span10">
						{form_input name="url_ja" class="span12"}
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="banner_attribute"}
					</div>
					<div class="span10">
						{form_input name="banner_attribute"}
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="date_start"}
					</div>
					<div class="span10">
						<input type="text" name="date_start" value="{$app.row.date_start}" class="jquery-ui-datetimepicker">
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="date_end"}
					</div>
					<div class="span10">
						<input type="text" name="date_end" value="{$app.row.date_end}" class="jquery-ui-datetimepicker">
					</div>
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
</body>
</html>
