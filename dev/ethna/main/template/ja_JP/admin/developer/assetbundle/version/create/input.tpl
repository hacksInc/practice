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
			<h2>リソースバージョン&nbsp;開始設定&nbsp;-追加-</h2>
			<form action="confirm" method="post" class="form-horizontal">
			    <div class="control-group">
				    <label class="control-label">{form_name name="app_ver"}</label>
				    <div class="controls">
						<input type="text" name="app_ver" value="{$app.row.app_ver}">
					</div>
			    </div>
					
{*					
			    <div class="control-group">
				    <label class="control-label">{form_name name="res_ver"}</label>
				    <div class="controls">
						<input type="text" name="res_ver" value="{$app.row.res_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="mon_ver"}</label>
				    <div class="controls">
						<input type="text" name="mon_ver" value="{$app.row.mon_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="mon_image_ver"}</label>
				    <div class="controls">
						<input type="text" name="mon_image_ver" value="{$app.row.mon_image_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="skilldata_ver"}</label>
				    <div class="controls">
						<input type="text" name="skilldata_ver" value="{$app.row.skilldata_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="skilleffect_ver"}</label>
				    <div class="controls">
						<input type="text" name="skilleffect_ver" value="{$app.row.skilleffect_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="bgmodel_ver"}</label>
				    <div class="controls">
						<input type="text" name="bgmodel_ver" value="{$app.row.bgmodel_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="sound_ver"}</label>
				    <div class="controls">
						<input type="text" name="sound_ver" value="{$app.row.sound_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="map_ver"}</label>
				    <div class="controls">
						<input type="text" name="map_ver" value="{$app.row.map_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="worldmap_ver"}</label>
				    <div class="controls">
						<input type="text" name="worldmap_ver" value="{$app.row.worldmap_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="mon_exp_ver"}</label>
				    <div class="controls">
						<input type="text" name="mon_exp_ver" value="{$app.row.mon_exp_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="player_rank_ver"}</label>
				    <div class="controls">
						<input type="text" name="player_rank_ver" value="{$app.row.player_rank_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="ach_ver"}</label>
				    <div class="controls">
						<input type="text" name="ach_ver" value="{$app.row.ach_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="mon_act_ver"}</label>
				    <div class="controls">
						<input type="text" name="mon_act_ver" value="{$app.row.mon_act_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="boost_ver"}</label>
				    <div class="controls">
						<input type="text" name="boost_ver" value="{$app.row.boost_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="badge_ver"}</label>
				    <div class="controls">
						<input type="text" name="badge_ver" value="{$app.row.badge_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="badge_material_ver"}</label>
				    <div class="controls">
						<input type="text" name="badge_material_ver" value="{$app.row.badge_material_ver}">
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="badge_skill_ver"}</label>
				    <div class="controls">
						<input type="text" name="badge_skill_ver" value="{$app.row.badge_skill_ver}">
					</div>
			    </div>
*}
				{foreach from=$app.res_ver_keys item="key"}
					<div class="control-group">
						<label class="control-label">{form_name name=$key}</label>
						<div class="controls">
							<input type="text" name="{$key}" value="{$app.row.$key}">
						</div>
					</div>
				{/foreach}

			    <div class="control-group">
				    <label class="control-label">
						{form_name name="clear"}
						<span class="help-block">※キャッシュクリアを行うと全更新が行われます注意して下さい</span>
					</label>
				    <div class="controls">
						<select name="clear">
							{html_options options=$app.clear_options selected=$app.row.clear}
						</select>
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="date_start"}</label>
				    <div class="controls">
						<input type="text" name="date_start" value="{$app.row.date_start}" class="jquery-ui-datetimepicker">
					</div>
			    </div>
			    <div class="control-group">
				    <div class="controls">
					   <input type="submit" value="リリースバージョン確認" class="btn" />
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