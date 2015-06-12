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
			<h2>おまけガチャ&nbsp;モンスター&nbsp;登録</h2>
			
			{if count($errors) > 0}
				<p class="text-error">
				{foreach from=$errors item=error}
					{$error|nl2br}<br />
				{/foreach}
				</p>
			{/if}
			
			<form action="confirm" method="post" class="form-horizontal">
			    <div class="control-group">
				    <label class="control-label">{form_name name="gacha_id"}</label>
				    <div class="controls">
						<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
						{$form.gacha_id}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="input-rarity">{form_name name="rarity"}</label>
				    <div class="controls">
						（モンスターIDより設定）
				    </div>
			    </div>
			    <div class="control-group {if is_error('monster_id')}error{/if}">
				    <label class="control-label" for="input-monster-id">{form_name name="monster_id"}</label>
				    <div class="controls">
						<input type="text" name="monster_id" id="input-monster-id" value="{$form.monster_id}">
						{*<span class="help-inline">{message name="monster-id"}</span>*}
				    </div>
			    </div>
			    <div class="control-group {if is_error('monster_lv')}error{/if}">
				    <label class="control-label" for="input-monster-lv">{form_name name="monster_lv"}</label>
				    <div class="controls">
						<input type="text" name="monster_lv" id="input-monster-lv" value="{$form.monster_lv}">
						{*<span class="help-inline">{message name="monster-lv"}</span>*}
				    </div>
			    </div>
			    <div class="control-group {if is_error('weight_float')}error{/if}">
				    <label class="control-label" for="input-weight">{form_name name="weight_float"}<i class="icon-question-sign" data-original-title="少数2桁まで"></i></label>
				    <div class="controls">
						<input type="text" name="weight_float" id="input-weight" value="{$form.weight_float}">
						{*<span class="help-inline">{message name="weight_float"}</span>*}
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

							{foreach from=$app.gacha_extra_category_list item="row" key="i" name="loop1"}
								<tr {*{if is_error('rarity') and ($form.rarity == $row.rarity)}class="error"{/if}*}>
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
					   <input type="submit" value="登録確認" class="btn" />
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