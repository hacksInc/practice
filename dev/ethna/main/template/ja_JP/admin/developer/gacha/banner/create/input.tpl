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
			<h2>ガチャマスター&nbsp;登録</h2>
			<form action="confirm" method="post" enctype="multipart/form-data" class="form-horizontal">
				{if $form.gacha_id}
					<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
				{/if}
				
				<div class="row-fluid">
					<div class="span6">
					</div>
					<div class="span2">
						{form_name name="lang"}
					</div>
					<div class="span4">
						{form_input name="lang"}
					</div>
				</div>
					
				<br>
				<div class="row-fluid">
					<div class="span6">
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
						{form_name name="gacha_id"}
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
					<div class="span2">
						{form_name name="sort_list"}
					</div>
					<div class="span4">
						{form_input name="sort_list" default=$app.row.sort_list}
					</div>
					<div class="span2">
						{form_name name="price"}
					</div>
					<div class="span4">
						{form_input name="price" default=$app.row.price}
					</div>
				</div>
							
				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="comment"}
					</div>
					<div class="span10">
						{*
						{form_input name="comment" class="span12"}
						*}
						<input class="span12" type="text" name="comment" value="{$app.row.comment}" />
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

				{if $form.gacha_id}
				<br>
			    <div class="text-center">
					<img src="../image?gacha_id={$form.gacha_id}">
			    </div>
				{/if}

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="banner_type"}
					</div>
					<div class="span4">
						{form_input name="banner_type" default=$app.row.banner_type}
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="banner_url"}
					</div>
					<div class="span10">
						<input class="span12" type="text" name="banner_url" value="{$app.row.banner_url}" />
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="width"}
					</div>
					<div class="span4">
						{form_input name="width" default=$app.row.width}
					</div>
					<div class="span2">
						{form_name name="height"}
					</div>
					<div class="span4">
						{form_input name="height" default=$app.row.height}
					</div>
				</div>
							
				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="position_x"}
					</div>
					<div class="span4">
						{form_input name="position_x" default=$app.row.position_x}
					</div>
					<div class="span2">
						{form_name name="position_y"}
					</div>
					<div class="span4">
						{form_input name="position_y" default=$app.row.position_y}
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
				   <input type="submit" value="登録確認" class="btn" />
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