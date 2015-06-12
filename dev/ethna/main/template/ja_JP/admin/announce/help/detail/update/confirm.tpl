<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ヘルプ詳細文 - サイコパス管理ページ"}
<body>
<link href="/psychopass_game/css/admin/announce/text-inverse.css" rel="stylesheet">
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ヘルプ詳細文データ登録&nbsp;確認</h2>
			<form action="exec" method="post" class="form-horizontal">
				<input type="hidden" name="confirm_uniq_picture" value="{$app.confirm_uniq_picture}">
				<input type="hidden" name="picture_uploaded" value="{$app.picture_uploaded}">
				<input type="hidden" name="help_id" value="{$form.help_id}">

			    <div class="row-fluid">
					<div class="span4">
						<input type="hidden" name="priority" value="{$form.priority}">
						{form_name name="priority"}
						{$app.form_template.priority.option[$form.priority]}
					</div>
					<div class="span4">
						<input type="hidden" name="category_id" value="{$form.category_id}">
						{form_name name="category_id"}
						{$app.category_list[$form.category_id]}
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
					{form_name name="body"}
				</div>
				<div>
					<input type="hidden" name="body" value="{$form.body}">
					<div class="admin-announce-text-inverse">{$app_ne.body}</div>
				</div>

				{if $app.picture_uploaded || (!$app.picture_uploaded && $app.row.picture)}
				<br>
				<div style="text-align:center;">
					<img src="{if $app.picture_uploaded}{$app.picture_data}{else}../image?img_id={$app.row.help_id}&type=picture&dummy={$app.mtime}{/if}">
				</div>
				{/if}

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
