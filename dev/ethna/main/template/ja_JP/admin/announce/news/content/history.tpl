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
			<form action="history" method="post" class="text-right" id="form-lu">
				{form_input name="lu" id="select-lu"}
			</form>

			<h2>お知らせデータログ</h2>

			{foreach from=$app.list item="row" key="i"}
				<div class="row-fluid" style="border-top: solid 1px; border-left: solid 1px; border-right: solid 1px;">
					<div class="row-fluid">
				        <div class="span1">
							&nbsp;
						</div>
						<div class="span11">
							<div class="row-fluid">
								<div class="span2">
									{form_name name="priority"}
								</div>
								<div class="span4">
									{$app.form_template.priority.option[$row.priority]}
								</div>
								<div class="span2">
									{form_name name="date_disp"}
								</div>
								<div class="span4">
									{$row.date_disp}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="title"}
								</div>
								<div class="span10">
									<div class="admin-announce-text-inverse">{$app_ne.list[$i].title}</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="admin-announce-text-inverse">{$app_ne.list[$i].abridge}</div>
					</div>
					<div class="row-fluid">
						<div class="admin-announce-text-inverse">{$app_ne.list[$i].body}</div>
					</div>
					<div class="row-fluid">
						&nbsp;
					</div>
					<div class="row-fluid text-center">
						<img src="image?img_id={$row.content_id}&type=banner&dummy={$app.mtime}">
					</div>
					<div class="row-fluid text-center">
						<img src="image?img_id={$row.content_id}&type=picture&dummy={$app.mtime}">
					</div>
					<div class="row-fluid">
						<div class="span10">
							<div class="row-fluid">
								<div class="span2">
									{form_name name="date_start"}
								</div>
								<div class="span4">
									{$row.date_start}
								</div>
								<div class="span2">
									{form_name name="date_end"}
								</div>
								<div class="span4">
									{$row.date_end}
								</div>
							</div>
						</div>
				        <div class="span1">
							<form action="create/input" method="get">
								{*{if $form.lu}<input type="hidden" name="lu0" value="{$form.lu}">{/if}*}
								<input type="hidden" name="content_id" value="{$row.content_id}">
								<input type="submit" value="複製" class="btn">
							</form>
						</div>
				        <div class="span1">
							&nbsp;
						</div>
					</div>
				</div>
			{/foreach}
			<div class="row-fluid" style="border-top: solid 1px;">
				&nbsp;
			</div>

			<div class="text-center">
				{$app_ne.pager.all}
			</div>

			<div class="text-right">
				{a href="index"}データ一覧へ戻る{/a}
			</div>

        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{literal}
<script>
	$(function(){
		$('#select-lu').change(function(){
			$('#form-lu').submit();
		});
	});
</script>
{/literal}
</body>
</html>
