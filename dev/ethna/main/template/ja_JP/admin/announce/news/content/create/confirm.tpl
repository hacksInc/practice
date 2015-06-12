<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="お知らせ - サイコパス管理ページ"}
<body>
<link href="/psychopass_game/css/admin/announce/text-inverse.css" rel="stylesheet">
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>お知らせデータ登録&nbsp;確認</h2>
			<form action="exec" method="post" class="form-horizontal">
			    <div class="row-fluid">
					<div class="span6">
						<input type="hidden" name="priority" value="{$form.priority}">
						{form_name name="priority"}
						{$app.form_template.priority.option[$form.priority]}
					</div>
					<div class="span6">
						{*
						<input type="hidden" name="date_disp" value="{$form.date_disp}">
						{form_name name="date_disp"}
						{$form.date_disp}
						*}
					</div>
				</div>

				<br>
				<div>
					{form_name name="title"}
				</div>
				<div>
					<input type="hidden" name="title" value="{$form.title}">
					<div class="admin-announce-text-inverse">{$app_ne.title}</div>
				</div>

				<div>
					{form_name name="abridge"}
				</div>
				<div>
					<input type="hidden" name="abridge" value="{$form.abridge}">
					<div class="admin-announce-text-inverse">{$app_ne.abridge}</div>
				</div>

				<br>
				<div style="text-align:center;">
					<input type="hidden" name="confirm_uniq_banner" value="{$app.confirm_uniq_banner}">
					{if $app.banner_data!=null}
						<img src="{$app.banner_data}">
					{else}
						{if $app.banner_no!=null}
							<input type="hidden" name="banner_no" value="{$app.banner_no}">
							<img src="../image?img_id={$app.banner_no}&type=banner&dummy={$app.mtime}">
						{/if}
					{/if}
				</div>

				<br>
				<div style="text-align:center;">
					<input type="hidden" name="confirm_uniq_picture" value="{$app.confirm_uniq_picture}">
					{if $app.picture_data!=null}
						<img src="{$app.picture_data}">
					{else}
						{if $app.picture_no!=null}
							<input type="hidden" name="picture_no" value="{$app.picture_no}">
							<img src="../image?img_id={$app.picture_no}&type=picture&dummy={$app.mtime}">
						{/if}
					{/if}
				</div>

				<div>
					{form_name name="body"}
				</div>
				<div>
					<input type="hidden" name="body" value="{$form.body}">
					<div class="admin-announce-text-inverse">{$app_ne.body}</div>
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
