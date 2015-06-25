<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="プレゼント配布管理 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>プレゼント配布　ログ</h2>

			{foreach from=$app.list item="row" key="i"}
				<div class="row-fluid" style="border-top: solid 1px; border-left: solid 1px; border-right: solid 1px;">
					<div class="row-fluid">
						<div class="span9">
							管理ID：{$app.list[$i].present_mng_id}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;配布ステータス：
								{if $app.list[$i].status==0}<font color="#ff0000">配布開始</font>{/if}
								{if $app.list[$i].status==1}配布中止{/if}
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							配布登録：{$app.list[$i].account_regist}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;更新：{$app.list[$i].account_update}{if $app.list[$i].account_update==NULL}????????{/if}
							<br />
							対象ユーザー：
								{if $app.list[$i].target_type==0}全ユーザー{/if}
								{if $app.list[$i].target_type==1}指定期間アクセスユーザー&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$app.list[$i].access_date_start}&nbsp;～&nbsp;{$app.list[$i].access_date_end}{/if}
								{if $app.list[$i].target_type==2}ユーザーID指定&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$app.list[$i].pp_id}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$app.list[$i].nickname}{/if}
							<br />
							配布期間：
							{if $app.list[$i].dist_term==1}<font color="#ff0000">{/if}
							{$app.list[$i].distribute_date_start}&nbsp;～&nbsp;{$app.list[$i].distribute_date_end}
							{if $app.list[$i].dist_term==1}</font>{/if}
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;配布人数：{$app.list[$i].distribute_user_total}人
							<br />
							配布内容：{$app.list[$i].present_values}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;配布数：
								{if $app.list[$i].present_category == 2}
									{$app.list[$i].num}枚&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;配布ID：{$app.list[$i].item_id}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$app.list[$i].photo_name}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;レベル：{$app.list[$i].lv}
								{elseif $app.list[$i].present_category == 3}
									{$app.list[$i].num}ポイント
								{else}
									{$app.list[$i].num}個
								{/if}
							<br />
							コメントID：{$app.list[$i].comment_ids}
								{if $app.list[$i].comment_id==0}
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;自由文：「{$app.list[$i].comment}」
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
				{a href="index"}配布管理データ一覧へ戻る{/a}
			</div>

        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{literal}
<script>
</script>
{/literal}
</body>
</html>
