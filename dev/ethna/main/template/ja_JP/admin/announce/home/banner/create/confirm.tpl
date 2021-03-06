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
			<h2>メインバナー登録確認</h2>
			<form action="exec" method="post" class="form-horizontal">
				<input type="hidden" name="confirm_uniq" value="{$app.confirm_uniq}">

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
						<input type="hidden" name="ua" value="{$form.ua}">
						{$app.form_template.ua.option[$form.ua]}
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
						<input type="hidden" name="type" value="{$form.type}"> 
						{$app.form_template.type.option[$form.type]}
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
						<input type="hidden" name="pri" value="{$form.pri}">
						{$app.form_template.pri.option[$form.pri]}
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="memo"}
					</div>
					<div class="span10">
						<input type="hidden" name="memo" value="{$form.memo}">
						{$form.memo}
					</div>
				</div>

				<br>
				<div style="text-align:center;">
					<img src="{$app.banner_data}">
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="url_ja"}
					</div>
					<div class="span10">
						<input type="hidden" name="url_ja" value="{$form.url_ja}">
						{$form.url_ja}
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="banner_attribute"}
					</div>
					<div class="span10">
						<input type="hidden" name="banner_attribute" value="{$form.banner_attribute}">
						{$form.banner_attribute}
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="date_start"}
					</div>
					<div class="span10">
						<input type="hidden" name="date_start" value="{$form.date_start}">
						{$form.date_start}
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="date_end"}
					</div>
					<div class="span10">
						<input type="hidden" name="date_end" value="{$form.date_end}">
						{$form.date_end}
					</div>
				</div>

				<br>
				<div class="text-center">
				   <input type="submit" value="登録" class="btn" />
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
