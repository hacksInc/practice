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
			<h2>ログインボーナス　登録画面</h2>
			<form action="confirm" method="post" class="form-horizontal">
			
			<h4>ログインボーナスID</h4>
			<input type="text" name="login_bonus_id" value="{$form.login_bonus_id}" />任意の値を入れて下さい（初期値は最大値+1）
			
			<h4>名称</h4>
			<input type="text" name="name" value="{$form.name}" />
			
			<h4>配布期間</h4>
				<div class="date_start">
					開始日&nbsp;&nbsp;
					<input type="text" name="date_start" value="{$form.date_start}" class="jquery-ui-datetimepicker">
					&nbsp;&nbsp;～&nbsp;&nbsp;
					終了日&nbsp;&nbsp;
					<input type="text" name="date_end" value="{$form.date_end}" class="jquery-ui-datetimepicker">
					<br />
				</div>
			<br />
			
			{section name=stamp loop=$app.data}
			
			<h4>スタンプ　{$smarty.section.stamp.index+1}日目　配布内容</h4>
				<div class="dist_type">
					<select name="dist_type{$smarty.section.stamp.index}">
						{html_options options=$app.dist_type_options selected=$app.data[stamp].dist_type}
					</select>
					&nbsp;&nbsp;&nbsp;&nbsp;配布数<input type="text" name="number{$smarty.section.stamp.index}" value="{$app.data[stamp].number}">
					<br />
					モンスターID<input type="text" name="item_id{$smarty.section.stamp.index}" value="{$app.data[stamp].item_id}">
					&nbsp;&nbsp;&nbsp;&nbsp;レベル<input type="text" name="lv{$smarty.section.stamp.index}" value="{$app.data[stamp].lv}">
					<br />
				</div>
			<br />
			
			{/section}
			
				<div class="text-center">
					<input type="submit" value="配布確認" class="btn" />
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