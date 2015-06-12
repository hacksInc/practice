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
			<h2>メインバナーログ</h2>

			{foreach from=$app.list item="row" key="i"}
			<div class="row-fluid" style="border-top: solid 1px; border-left: solid 1px; border-right: solid 1px;">
				<div class="row-fluid">
					<div class="span1">
						&nbsp;
					</div>
					<div class="span11">
						<div class="row-fluid">
							<div class="span2">
								{form_name name="hbanner_id"}
							</div>
							<div class="span4">
								{$row.hbanner_id}
							</div>
							<div class="span2">
								{form_name name="ua"}
							</div>
							<div class="span4">
								{$app.form_template.ua.option[$row.ua]}
							</div>
						</div>
						<div class="row-fluid">
							<div class="span6">
								&nbsp;
							</div>
							<div class="span2">
								{form_name name="pri"}
							</div>
							<div class="span4">
								{$app.form_template.pri.option[$row.pri]}
							</div>
						</div>
						<div class="row-fluid">
							<div class="span2">
								{form_name name="memo"}
							</div>
							<div class="span10">
								{$row.memo}
							</div>
						</div>
						<div class="row-fluid">
							<div class="span2">
								{form_name name="url_ja"}
							</div>
							<div class="span10">
								{$row.url_ja}
							</div>
						</div>
						<div class="row-fluid">
							<div class="span2">
								{form_name name="banner_attribute"}
							</div>
							<div class="span10">
								{$row.banner_attribute}
							</div>
						</div>
					</div>
				</div>
				<div class="row-fluid text-center">
					<img src="image?img_id={$row.img_id}&dummy={$app.mtime}">
				</div>
				<div class="row-fluid">
					&nbsp;
				</div>
				<div class="row-fluid">
					<div class="span11">
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
						<form action="end/exec" method="post">
							&nbsp;
						</form>
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
				{a href="index"}戻る{/a}
			</div>

		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
