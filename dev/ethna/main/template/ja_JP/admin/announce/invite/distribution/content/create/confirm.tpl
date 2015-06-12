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
			<h2>招待特典　確認画面</h2>
			<form action="exec" method="post" class="form-horizontal">
			
			{if $form.invite_mng_id >= 0}
				<h4>管理ID&nbsp;&nbsp;{$form.invite_mng_id}</h4>
			{/if}
			<input type="hidden" name="invite_mng_id" value="{$form.invite_mng_id}" />
			
			<h4>付与日時</h4>
				&nbsp;&nbsp;
				{$form.date_start}
					～
				{$form.date_end}
				<input type="hidden" name="date_start" value="{$form.date_start}">
				<input type="hidden" name="date_end" value="{$form.date_end}">
			
			<h4>付与内容</h4>
				&nbsp;&nbsp;
				同一招待者最大付与回数
				&nbsp;&nbsp;
				{$form.invite_max}回
				<input type="hidden" name="invite_max" value="{$form.invite_max}">
				<h5>被招待者</h5>
				&nbsp;&nbsp;
				{$app.g_dist_type}
				<input type="hidden" name="g_dist_type" value="{$form.g_dist_type}">
				<input type="hidden" name="g_number" value="{$form.g_number}">
				&nbsp;&nbsp;
				{if $form.g_dist_type < 4}
					{$form.g_number}枚
					<input type="hidden" name="g_item_id" value="0">
					<input type="hidden" name="g_lv" value="1">
				{else}
					{$form.g_number}体
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					モンスターID&nbsp;&nbsp;{$form.g_item_id}&nbsp;&nbsp;{$app.g_monster_name}
					{if $app.g_monster_name==NULL}<font color="#ff0000">モンスターが存在しません！</font>{/if}
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					レベル&nbsp;&nbsp;{$form.g_lv}
					<input type="hidden" name="g_item_id" value="{$form.g_item_id}">
					<input type="hidden" name="g_lv" value="{$form.g_lv}">
				{/if}
				<h5>招待者</h5>
				&nbsp;&nbsp;
				{$app.i_dist_type}
				<input type="hidden" name="i_dist_type" value="{$form.i_dist_type}">
				<input type="hidden" name="i_number" value="{$form.i_number}">
				&nbsp;&nbsp;
				{if $form.i_dist_type < 4}
					{$form.i_number}枚
					<input type="hidden" name="i_item_id" value="0">
					<input type="hidden" name="i_lv" value="1">
				{else}
					{$form.i_number}体
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					モンスターID&nbsp;&nbsp;{$form.i_item_id}&nbsp;&nbsp;{$app.i_monster_name}
					{if $app.i_monster_name==NULL}<font color="#ff0000">モンスターが存在しません！</font>{/if}
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					レベル&nbsp;&nbsp;{$form.i_lv}
					<input type="hidden" name="i_item_id" value="{$form.i_item_id}">
					<input type="hidden" name="i_lv" value="{$form.i_lv}">
				{/if}
				<br />
				<br />
				<div class="text-center">
					{if $form.g_dist_type==4 && $app.g_monster_name==NULL}
						&nbsp;&nbsp;
					{elseif $form.i_dist_type==4 && $app.i_monster_name==NULL}
						&nbsp;&nbsp;
					{else}
						<input type="submit" value="付与登録" class="btn" />
					{/if}
				</div>
			</form>
			<form action="input2" method="post" class="form-horizontal">
				<div class="text-center">
					<input type="hidden" name="invite_mng_id" value="{$form.invite_mng_id}" />
					<input type="hidden" name="date_start" value="{$form.date_start}">
					<input type="hidden" name="date_end" value="{$form.date_end}">
					<input type="hidden" name="invite_max" value="{$form.invite_max}">
					<input type="hidden" name="g_dist_type" value="{$form.g_dist_type}">
					<input type="hidden" name="g_number" value="{$form.g_number}">
					{if $form.g_dist_type < 4}
						<input type="hidden" name="g_item_id" value="0">
						<input type="hidden" name="g_lv" value="1">
					{else}
						<input type="hidden" name="g_item_id" value="{$form.g_item_id}">
						<input type="hidden" name="g_lv" value="{$form.g_lv}">
					{/if}
					<input type="hidden" name="i_dist_type" value="{$form.i_dist_type}">
					<input type="hidden" name="i_number" value="{$form.i_number}">
					{if $form.i_dist_type < 4}
						<input type="hidden" name="i_item_id" value="0">
						<input type="hidden" name="i_lv" value="1">
					{else}
						<input type="hidden" name="i_item_id" value="{$form.i_item_id}">
						<input type="hidden" name="i_lv" value="{$form.i_lv}">
					{/if}
					<input type="submit" value="修正" class="btn" />
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