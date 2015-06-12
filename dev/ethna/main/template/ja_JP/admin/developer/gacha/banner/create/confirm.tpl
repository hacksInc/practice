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
			<h2>ガチャマスター登録確認</h2>
			<form action="exec" method="post" class="form-horizontal">
				<input type="hidden" name="confirm_uniq" value="{$app.confirm_uniq}">
				
				{if $form.gacha_id}
					<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
				{/if}
				
				<br>
				<div class="row-fluid">
					<div class="span6">
					</div>
					<div class="span2">
						{form_name name="lang"}
					</div>
					<div class="span4">
						<input type="hidden" name="lang" value="{$form.lang}">
						{$app.form_template.lang.option[$form.lang]}
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
						<input type="hidden" name="ua" value="{$form.ua}">
						{$app.form_template.ua.option[$form.ua]}
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
						<input type="hidden" name="type" value="{$form.type}"> 
						{$app.form_template.type.option[$form.type]}
					</div>
				</div>

				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="sort_list"}
					</div>
					<div class="span4">
						<input type="hidden" name="sort_list" value="{$form.sort_list}"> 
						{$app.form_template.sort_list.option[$form.sort_list]}
					</div>
					<div class="span2">
						{form_name name="price"}
					</div>
					<div class="span4">
						<input type="hidden" name="price" value="{$form.price}"> 
						{$form.price}
					</div>
				</div>
							
				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="comment"}
					</div>
					<div class="span10">
						<input type="hidden" name="comment" value="{$form.comment}"> 
						{$form.comment}
					</div>
				</div>
							
				<br>
				<div style="text-align:center;">
					<img src="{$app.banner_data}">
				</div>
							
				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="banner_type"}
					</div>
					<div class="span10">
						<input type="hidden" name="banner_type" value="{$form.banner_type}">
						{$app.form_template.banner_type.option[$form.banner_type]}
					</div>
				</div>
					
				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="banner_url"}
					</div>
					<div class="span10">
						<input type="hidden" name="banner_url" value="{$form.banner_url}"> 
						{$form.banner_url}
					</div>
				</div>
					
				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="width"}
					</div>
					<div class="span4">
						<input type="hidden" name="width" value="{$form.width}"> 
						{$form.width}
					</div>
					<div class="span2">
						{form_name name="height"}
					</div>
					<div class="span4">
						<input type="hidden" name="height" value="{$form.height}"> 
						{$form.height}
					</div>
				</div>
					
				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="position_x"}
					</div>
					<div class="span4">
						<input type="hidden" name="position_x" value="{$form.position_x}"> 
						{$form.position_x}
					</div>
					<div class="span2">
						{form_name name="position_y"}
					</div>
					<div class="span4">
						<input type="hidden" name="position_y" value="{$form.position_y}"> 
						{$form.position_y}
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