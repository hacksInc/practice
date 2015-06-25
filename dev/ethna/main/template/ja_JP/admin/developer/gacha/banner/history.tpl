<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ガチャ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ガチャマスターログ</h2>
			
			{foreach from=$app.list item="row" key="i"}
				<div class="row-fluid" style="border-top: solid 1px; border-left: solid 1px; border-right: solid 1px;">
					<div class="row-fluid">
						<div class="span1">
							&nbsp;
						</div>
						<div class="span11">
							<div class="row-fluid">
								<div class="span6">
								</div>
								<div class="span2">
									{form_name name="ua"}
								</div>
								<div class="span4">
									{$app.form_template.ua.option[$row.ua]}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="gacha_id"}
								</div>
								<div class="span4">
									{$row.gacha_id}
								</div>
								<div class="span2">
									{form_name name="type"}
								</div>
								<div class="span4">
									{$app.form_template.type.option[$row.type]}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="sort_list"}
								</div>
								<div class="span4">
									{$row.sort_list}
								</div>
								<div class="span2">
									{form_name name="price"}
								</div>
								<div class="span4">
									{$row.price}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="comment"}
								</div>
								<div class="span10">
									{$row.comment}
								</div>
							</div>
						</div>
					</div>
					<div class="row-fluid text-center">
						<img src="image?gacha_id={$row.gacha_id}">
					</div>
					<div class="row-fluid">
						&nbsp;
					</div>
					<div class="row-fluid">
						<div class="span1">
							&nbsp;
						</div>
						<div class="span11">
							<div class="row-fluid">
								<div class="span2">
									{form_name name="banner_type"}
								</div>
								<div class="span4">
									{$app.form_template.banner_type.option[$row.banner_type]}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="banner_url"}
								</div>
								<div class="span10">
									{$row.banner_url}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="width"}
								</div>
								<div class="span4">
									{$row.width}
								</div>
								<div class="span2">
									{form_name name="height"}
								</div>
								<div class="span4">
									{$row.height}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="position_x"}
								</div>
								<div class="span4">
									{$row.position_x}
								</div>
								<div class="span2">
									{form_name name="position_y"}
								</div>
								<div class="span4">
									{$row.position_y}
								</div>
							</div>
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