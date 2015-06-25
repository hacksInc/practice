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
			<h2>ガチャ&nbsp;モンスター&nbsp;登録確認</h2>
			
			<form action="exec" method="post" class="form-horizontal">
			    <div class="control-group">
				    <label class="control-label">{form_name name="gacha_id"}</label>
				    <div class="controls">
						<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
						{$form.gacha_id}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="rarity"}</label>
				    <div class="controls">
						<input type="hidden" name="rarity" value="{$app.rarity}">
						{$app.rarity}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="monster_id"}</label>
				    <div class="controls">
						<input type="hidden" name="monster_id" value="{$form.monster_id}">
						{$form.monster_id}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="monster_lv"}</label>
				    <div class="controls">
						<input type="hidden" name="monster_lv" value="{$form.monster_lv}">
						{$form.monster_lv}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="weight_float"}</label>
				    <div class="controls">
						<input type="hidden" name="weight_float" value="{$form.weight_float}">
						{$form.weight_float}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">枚数</label>
				    <div class="controls">
						{$app.number_of_monsters}
				    </div>
			    </div>
					
				<div class="row-fluid">
					<div class="span6">
						現在設定されている、レアリティごとの登録数<br>
						既に登録されている同じレベルのモンスターIDの登録は行えません。
						<table class="table table-condensed">
							<tr>
								<th>{form_name name="rarity"}</th>
								<th>{form_name name="weight_float"}</th>
								<th>登録数</th>
							</tr>

							{foreach from=$app.gacha_category_list item="row" key="i" name="loop1"}
								<tr>
									<td><div class="text-right">{$row.rarity}</div></td>
									<td><div class="text-right">{$row.weight_float|string_format:"%.2f"}</div></td>
									<td><div class="text-right">{$row.number_of_monsters}</div></td>
								</tr>
							{/foreach}
						</table>
					</div>
				</div>
						
			    <div class="control-group">
				    <div class="controls">
					   <input type="submit" value="登録" class="btn" />
				    </div>
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