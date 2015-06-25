<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="リソースバージョン操作ログ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>リソースバージョン操作ログ</h2>
			<table class="table">
				<tr>
					<th rowspan="2">日時	</th>
					<th rowspan="2">アカウント</th>
					<th colspan="14">操作内容</th>
				</tr>
				<tr>
					<th>Action</th>
					<th>{form_name name="date_start"}</th>
					<th>{form_name name="app_ver"}</th>
{*					
					<th>{form_name name="res_ver"}</th>
					<th>{form_name name="mon_ver"}</th>
					<th>{form_name name="mon_image_ver"}</th>
					<th>{form_name name="skilldata_ver"}</th>
					<th>{form_name name="skilleffect_ver"}</th>
					<th>{form_name name="bgmodel_ver"}</th>
					<th>{form_name name="sound_ver"}</th>
					<th>{form_name name="map_ver"}</th>
					<th>{form_name name="worldmap_ver"}</th>
					<th>{form_name name="mon_exp_ver"}</th>
					<th>{form_name name="player_rank_ver"}</th>
					<th>{form_name name="ach_ver"}</th>
					<th>{form_name name="mon_act_ver"}</th>
					<th>{form_name name="boost_ver"}</th>
					<th>{form_name name="badge_ver"}</th>
					<th>{form_name name="badge_material_ver"}</th>
					<th>{form_name name="badge_skill_ver"}</th>
*}					
					{foreach from=$app.res_ver_keys item="key"}
						<th>{form_name name=$key}</th>
					{/foreach}
					<th>{form_name name="clear"}</th>
				</tr>
				
				{foreach from=$app.list item="row"}
				<tr>
					<td>{$row.time}</td>
					<td>{$row.user}</td>
					<td>{$row.action_type}</td>
					
					<td>{$row.date_start|default:"-"|substr:0:10}</td>
					<td>{$row.app_ver}</td>
{*
					<td>{$row.res_ver}</td>
					<td>{$row.mon_ver}</td>
					<td>{$row.mon_image_ver}</td>
					<td>{$row.skilldata_ver}</td>
					<td>{$row.skilleffect_ver}</td>
					<td>{$row.bgmodel_ver}</td>
					<td>{$row.sound_ver}</td>
					<td>{$row.map_ver}</td>
					<td>{$row.worldmap_ver}</td>
					<td>{$row.mon_exp_ver}</td>
					<td>{$row.player_rank_ver}</td>
					<td>{$row.ach_ver}</td>
					<td>{$row.mon_act_ver}</td>
					<td>{$row.boost_ver}</td>
					<td>{$row.badge_ver}</td>
					<td>{$row.badge_material_ver}</td>
					<td>{$row.badge_skill_ver}</td>
*}
					{foreach from=$app.res_ver_keys item="key"}
						<td>{$row.$key}</td>
					{/foreach}
					<td {if $row.clear}style="background-color:#f00;"{/if}>{$app.clear_options[$row.clear]}</td>
				</tr>
				{/foreach}
			</table>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
