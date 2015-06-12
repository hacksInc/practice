<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="リソースバージョン - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>リソースバージョン&nbsp;開始設定&nbsp;-追加確認-</h2>
			<form action="exec" method="post" class="form-horizontal">
			    <div class="control-group">
				    <label class="control-label">{form_name name="app_ver"}</label>
				    <div class="controls">
						{$form.app_ver}
						<input type="hidden" name="app_ver" value="{$form.app_ver}">
					</div>
			    </div>

{*
			    <div class="control-group">
				    <label class="control-label">{form_name name="res_ver"}</label>
				    <div class="controls">
						{$form.res_ver}
						<input type="hidden" name="res_ver" value="{$form.res_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="mon_ver"}</label>
				    <div class="controls">
						{$form.mon_ver}
						<input type="hidden" name="mon_ver" value="{$form.mon_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="mon_image_ver"}</label>
				    <div class="controls">
						{$form.mon_image_ver}
						<input type="hidden" name="mon_image_ver" value="{$form.mon_image_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="skilldata_ver"}</label>
				    <div class="controls">
						{$form.skilldata_ver}
						<input type="hidden" name="skilldata_ver" value="{$form.skilldata_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="skilleffect_ver"}</label>
				    <div class="controls">
						{$form.skilleffect_ver}
						<input type="hidden" name="skilleffect_ver" value="{$form.skilleffect_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="bgmodel_ver"}</label>
				    <div class="controls">
						{$form.bgmodel_ver}
						<input type="hidden" name="bgmodel_ver" value="{$form.bgmodel_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="sound_ver"}</label>
				    <div class="controls">
						{$form.sound_ver}
						<input type="hidden" name="sound_ver" value="{$form.sound_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="map_ver"}</label>
				    <div class="controls">
						{$form.map_ver}
						<input type="hidden" name="map_ver" value="{$form.map_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="worldmap_ver"}</label>
				    <div class="controls">
						{$form.worldmap_ver}
						<input type="hidden" name="worldmap_ver" value="{$form.worldmap_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="mon_exp_ver"}</label>
				    <div class="controls">
						{$form.mon_exp_ver}
						<input type="hidden" name="mon_exp_ver" value="{$form.mon_exp_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="player_rank_ver"}</label>
				    <div class="controls">
						{$form.player_rank_ver}
						<input type="hidden" name="player_rank_ver" value="{$form.player_rank_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="ach_ver"}</label>
				    <div class="controls">
						{$form.ach_ver}
						<input type="hidden" name="ach_ver" value="{$form.ach_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="mon_act_ver"}</label>
				    <div class="controls">
						{$form.mon_act_ver}
						<input type="hidden" name="mon_act_ver" value="{$form.mon_act_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="boost_ver"}</label>
				    <div class="controls">
						{$form.boost_ver}
						<input type="hidden" name="boost_ver" value="{$form.boost_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="badge_ver"}</label>
				    <div class="controls">
						{$form.badge_ver}
						<input type="hidden" name="badge_ver" value="{$form.badge_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="badge_material_ver"}</label>
				    <div class="controls">
						{$form.badge_material_ver}
						<input type="hidden" name="badge_material_ver" value="{$form.badge_material_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="badge_skill_ver"}</label>
				    <div class="controls">
						{$form.badge_skill_ver}
						<input type="hidden" name="badge_skill_ver" value="{$form.badge_skill_ver}">
					</div>
			    </div>
*}
				{foreach from=$app.res_ver_keys item="key"}
					<div class="control-group">
						<label class="control-label">{form_name name=$key}</label>
						<div class="controls">
							{$form.$key}
							<input type="hidden" name="{$key}" value="{$form.$key}">
						</div>
					</div>
				{/foreach}

			    <div class="control-group">
				    <label class="control-label">
						{form_name name="clear"}
						{if $form.clear}
						<span class="help-block">※キャッシュクリアを行うと全更新が行われます注意して下さい</span>
						{/if}
					</label>
				    <div class="controls">
						{$app.clear_options[$form.clear]}
						<input type="hidden" name="clear" value="{$form.clear}">
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="date_start"}</label>
					
				    <div class="controls">
						{$form.date_start}
						<input type="hidden" name="date_start" value="{$form.date_start}">
					</div>
			    </div>
			    <div class="control-group">
				    <div class="controls">
					   <input type="submit" value="リリースバージョン設定" class="btn" />
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