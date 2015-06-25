<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="リソースバージョン - サイコパス管理ページ"}
<body>
{literal}
<style type="text/css">
	.table td { text-align: center; }
</style>
{/literal}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>リソースバージョン&nbsp;ログ</h2>
			リリースしたもの、削除したものはログとして残します。<br>
			ログは最新100件まで。<br>
			<table class="table table-bordered table-striped table-condensed">
				<thead>
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
				</thead>
				<tbody>
				{strip}
					{foreach from=$app.list item="row" name="loop1"}
					<tr>
						<td>
							{if $row.date_deletion}
								削除
							{else}
								-
							{/if}
						</td>
						<td>{$row.date_start}</td>
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
				{/strip}
				</tbody>
			</table>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
