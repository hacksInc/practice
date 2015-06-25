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
			<h2>招待特典　ログ</h2>
			
			{foreach from=$app.list item="row" key="i"}
				<div class="row-fluid" style="border-top: solid 1px; border-left: solid 1px; border-right: solid 1px;">
					<div class="row-fluid">
						<div class="span9">
							管理ID：{$app.list[$i].invite_mng_id}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;付与ステータス：
								{if $app.list[$i].status==0}<font color="#ff0000">付与開始</font>{/if}
								{if $app.list[$i].status==1}付与中止{/if}
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							付与登録：{$app.list[$i].account_reg}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更新：{$app.list[$i].account_upd}{if $app.list[$i].account_upd==NULL}????????{/if}
							<br />
							付与期間：
							{if $app.list[$i].dist_term==1}<font color="#ff0000">{/if}
							{$app.list[$i].date_start}&nbsp;～&nbsp;{$app.list[$i].date_end}
							{if $app.list[$i].dist_term==1}</font>{/if}
							<br />
							同一招待者最大付与回数：{$app.list[$i].invite_max}回
							<br />
							被招待者付与回数：{$app.list[$i].g_dist_user_cnt}回<br />
							付与内容：{$app.list[$i].g_dist_types}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;付与数：
								{if $app.list[$i].g_dist_type < 4}
									{$app.list[$i].g_number}枚
								{else}
									{$app.list[$i].g_number}体&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;モンスターID：{$app.list[$i].g_item_id}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$app.list[$i].g_monster_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;レベル：{$app.list[$i].g_lv}
								{/if}
							<br />
							招待者付与回数：{$app.list[$i].i_dist_user_cnt}回&nbsp;&nbsp;&nbsp;&nbsp;招待者累計：{$app.list[$i].i_dist_user_total}回<br />
							付与内容：{$app.list[$i].i_dist_types}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;付与数：
								{if $app.list[$i].i_dist_type < 4}
									{$app.list[$i].i_number}枚
								{else}
									{$app.list[$i].i_number}体&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;モンスターID：{$app.list[$i].i_item_id}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$app.list[$i].i_monster_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;レベル：{$app.list[$i].i_lv}
								{/if}
							<br />
						</div>
					</div>
				</div>
			{/foreach}
			<div class="row-fluid" style="border-top: solid 1px;">
				&nbsp;
			</div>
			
			<div class="text-center">
				{$app_ne.pager.all}
			</div>
			
			<div class="text-right">
				{a href="index"}管理データ一覧へ戻る{/a}
			</div>
			
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{literal}
<script>
	$(function(){
		$('#select-lu').change(function(){
			$('#form-lu').submit();
		});
	});
</script>
{/literal}
</body>
</html>