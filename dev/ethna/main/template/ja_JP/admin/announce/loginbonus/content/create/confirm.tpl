<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ログインボーナス - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ログインボーナス　確認画面</h2>
			
			<font color="#ff0000">
			{section name=err loop=$app.err_msg}
				{$app.err_msg[err]}<br />
			{/section}
			</font>
			
			<h4>ログインボーナスID</h4>
			{$form.login_bonus_id}<br />
			
			<h4>名称</h4>
			{$form.name}<br />
			
			<h4>配布期間</h4>
				<div class="date_start">
					開始日&nbsp;&nbsp;
					{$form.date_start}
					&nbsp;&nbsp;～&nbsp;&nbsp;
					終了日&nbsp;&nbsp;
					{$form.date_end}
					<br />
				</div>
			<br />
			
			{section name=stamp loop=$app.data}
				<h4>スタンプ　{$smarty.section.stamp.index+1}日目　配布内容</h4>
					<div class="dist_type">
						{if $app.data[stamp].dist_type < 4}
							{$app.data[stamp].dist_type_name}&nbsp;&nbsp;&nbsp;&nbsp;{$app.data[stamp].number}枚<br />
						{else}
							{$app.data[stamp].dist_type_name}&nbsp;&nbsp;&nbsp;&nbsp;{$app.data[stamp].number}体<br />
							モンスターID
							&nbsp;&nbsp;&nbsp;&nbsp;
							{$app.data[stamp].item_id}
							&nbsp;&nbsp;&nbsp;&nbsp;
							{$app.data[stamp].monster_name}
							&nbsp;&nbsp;&nbsp;&nbsp;
							レベル{$app.data[stamp].lv}
							<br />
						{/if}
					</div>
				<br />
			{/section}
			
			<div class="text-center">
			<div class="span2">
			<form action="input2" method="post" class="form-horizontal">
				<input type="hidden" name="login_bonus_id" value="{$form.login_bonus_id}" />
				<input type="hidden" name="name" value="{$form.name}" />
				<input type="hidden" name="date_start" value="{$form.date_start}">
				<input type="hidden" name="date_end" value="{$form.date_end}">
				{section name=stamp loop=$app.data}
					<input type="hidden" name="dist_type{$smarty.section.stamp.index}" value="{$app.data[stamp].dist_type}" />
					<input type="hidden" name="number{$smarty.section.stamp.index}" value="{$app.data[stamp].number}" />
					<input type="hidden" name="item_id{$smarty.section.stamp.index}" value="{$app.data[stamp].item_id}" />
					<input type="hidden" name="lv{$smarty.section.stamp.index}" value="{$app.data[stamp].lv}" />
				{/section}
				<input type="submit" value="前の画面に戻る" class="btn" />
			</form>
			</div>
			<div class="span2">
			<form action="exec" method="post" class="form-horizontal">
				<input type="hidden" name="login_bonus_id" value="{$form.login_bonus_id}" />
				<input type="hidden" name="name" value="{$form.name}" />
				<input type="hidden" name="date_start" value="{$form.date_start}">
				<input type="hidden" name="date_end" value="{$form.date_end}">
				{section name=stamp loop=$app.data}
					<input type="hidden" name="dist_type{$smarty.section.stamp.index}" value="{$app.data[stamp].dist_type}" />
					<input type="hidden" name="number{$smarty.section.stamp.index}" value="{$app.data[stamp].number}" />
					<input type="hidden" name="item_id{$smarty.section.stamp.index}" value="{$app.data[stamp].item_id}" />
					<input type="hidden" name="lv{$smarty.section.stamp.index}" value="{$app.data[stamp].lv}" />
				{/section}
				<input type="submit" value="　　　設定　　　" class="btn" {if $app.err_chk==true}disabled="disabled"{/if} />
			</form>
			</div>
			</div>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
</body>
</html>