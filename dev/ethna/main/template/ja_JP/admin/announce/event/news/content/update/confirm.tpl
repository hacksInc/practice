<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="イベントのお知らせ - サイコパス管理ページ"}
<body>
<link href="/css/admin/announce/text-inverse.css" rel="stylesheet">
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>イベントのお知らせデータ修正&nbsp;確認</h2>
			<form action="exec" method="post" class="form-horizontal">
				<input type="hidden" name="confirm_uniq" value="{$app.confirm_uniq}">
				<input type="hidden" name="content_id" value="{$form.content_id}">

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
								<input type="hidden" name="ua" value="{$form.ua}"> 
								{$app.form_template.ua.option[$form.ua]}
							</div>
						</div>
					</div>
				</div>
				
			    <div class="row-fluid">
					<div class="span6">
						<div class="row-fluid">
							<div class="span3">
								{form_name name="priority"}
							</div>
							<div class="span9">
								<input type="hidden" name="priority" value="{$form.priority}">
								{$app.form_template.priority.option[$form.priority]}
							</div>
						</div>
					</div>
					<div class="span6">
						<div class="row-fluid">
							<div class="span3">
								{form_name name="date_disp"}
							</div>
							<div class="span9">
								<input type="hidden" name="date_disp" value="{$form.date_disp}">
								{$form.date_disp}
							</div>
						</div>
					</div>
				</div>
				
				<input type="hidden" name="banner_uploaded" value="{$app.banner_uploaded}">
				{if $app.banner_uploaded}
					<br>
<div class="admin-announce-text-inverse">
					<div style="text-align:center;">
						<img src="{$app.banner_data}">
					</div>
				{elseif $app.row.banner}
					<br>
					{if $form.banner_disabled}
						<div style="text-align:center;">
							<input type="hidden" name="banner_disabled" value="{$form.banner_disabled}">
							<div class="text-right"><i class="icon-ok"></i>バナー解除</div>
							<span style="font-size: 20px; font-weight:bold;">画像なし</span>
						</div>
<div class="admin-announce-text-inverse">
					{else}
<div class="admin-announce-text-inverse">
						<div style="text-align:center;">
							<img src="../image?content_id={$app.row.content_id}">
						</div>
					{/if}
				{else}
<div class="admin-announce-text-inverse">
				{/if}

				<br>
				<div style="text-align:center;">
					<input type="hidden" name="body" value="{$form.body}">
					{$app_ne.body}
				</div>
</div>
				
				<br>
				<div>
					<input type="hidden" name="date_start" value="{$form.date_start}">
					{form_name name="date_start"}
					{$form.date_start}
				</div>
				
				<div>
					<input type="hidden" name="date_end" value="{$form.date_end}">
					{form_name name="date_end"}
					{$form.date_end}
				</div>

				<br>
				<div class="text-center">
				   <input type="submit" value="修正" class="btn" />
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