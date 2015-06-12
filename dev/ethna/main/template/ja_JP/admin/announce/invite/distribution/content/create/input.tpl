<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="招待特典管理 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>招待特典　登録画面</h2>
			<form action="confirm" method="post" class="form-horizontal">
			
			{if $app.invite_mng_id >= 0}
				<h4>管理ID&nbsp;&nbsp;{$app.invite_mng_id}</h4>
				<input type="hidden" name="invite_mng_id" value="{$app.invite_mng_id}" />
			{else}
				<input type="hidden" name="invite_mng_id" value="-1" />
			{/if}
			
			<h4>付与日時</h4>
				<div class="date_start">
					<input type="text" name="date_start" value="{$app.date_start}" class="jquery-ui-datetimepicker">
					～
					<input type="text" name="date_end" value="{$app.date_end}" class="jquery-ui-datetimepicker">
					<br />
				</div>
			<br />
			
			<h4>付与内容</h4>
				<div class="dist_type">
					同一招待者最大付与回数<input type="text" name="invite_max" value="{$app.invite_max}">回
					<br />
					<br />
					<h5>被招待者</h5>
					<select name="g_dist_type">
						{html_options options=$app.dist_type_options selected=$app.row.g_dist_type}
					</select>
					<br />
					付与数<input type="text" name="g_number" value="{$app.g_number}">
					<br />
					モンスターID<input type="text" name="g_item_id" value="{$app.g_item_id}">（付与内容がモンスターの場合のみ）
					<br />
					レベル<input type="text" name="g_lv" value="{$app.g_lv}">（付与内容がモンスターの場合のみ）
					<br />
					<br />
					<h5>招待者</h5>
					<select name="i_dist_type">
						{html_options options=$app.dist_type_options selected=$app.row.i_dist_type}
					</select>
					<br />
					付与数<input type="text" name="i_number" value="{$app.i_number}">
					<br />
					モンスターID<input type="text" name="i_item_id" value="{$app.i_item_id}">（付与内容がモンスターの場合のみ）
					<br />
					レベル<input type="text" name="i_lv" value="{$app.i_lv}">（付与内容がモンスターの場合のみ）
					<br />
				</div>
				<br />
				<div class="text-center">
					<input type="submit" value="付与確認" class="btn" />
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