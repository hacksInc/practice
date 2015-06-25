<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="プレゼント配布 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>プレゼント配布　登録画面</h2>
			<form action="confirm" method="post" class="form-horizontal">

			{if $app.present_mng_id >= 0}
				<h4>配布管理ID&nbsp;&nbsp;{$app.present_mng_id}</h4>
				<input type="hidden" name="present_mng_id" value="{$form.present_mng_id}" />
			{else}
				<input type="hidden" name="present_mng_id" value="-1" />
			{/if}

			<h4>対象ユーザー</h4>
				<div class="trg_user">
					&nbsp;&nbsp;<input type="radio" name="target_type" value="0" {if $form.target_type==0}checked{/if} />全ユーザー
					<br />
					&nbsp;&nbsp;<input type="radio" name="target_type" value="1" {if $form.target_type==1}checked{/if} />指定期間アクセスユーザー
					&nbsp;&nbsp;<input type="text" name="access_date_start" value="{$form.access_date_start}" class="jquery-ui-datetimepicker">
					～
					<input type="text" name="access_date_end" value="{$form.access_date_end}" class="jquery-ui-datetimepicker">
					<br />
					&nbsp;&nbsp;<input type="radio" name="target_type" value="2" {if $form.target_type==2}checked{/if} />ユーザーID指定
					&nbsp;&nbsp;<input type="text" name="pp_id" value="{$form.pp_id}">
					<br />
				</div>
			<br />

			<h4>配布日時</h4>
				<div class="distribute_date_start">
					<input type="text" name="distribute_date_start" value="{$form.distribute_date_start}" class="jquery-ui-datetimepicker">
					～
					<input type="text" name="distribute_date_end" value="{$form.distribute_date_end}" class="jquery-ui-datetimepicker">
					<br />
				</div>
			<br />

			<h4>配布内容</h4>
				<div class="present_value">
					<select name="present_value">
						{html_options options=$app.present_value_options selected=$form.present_value}
					</select>
					<br />
					配布数<input type="text" name="num" value="{$form.num}">
					<br />
					配布ID<input type="text" name="item_id" value="{$form.item_id}">（配布内容がフォトの場合のみ）
					<br />
					&nbsp;&nbsp;&nbsp;&nbsp;レベル<input type="text" name="lv" value="{$form.lv}">（配布内容がフォトの場合のみ）
					<br />
					<br />
					コメント
					<select name="comment_id">
						{html_options options=$app.comment_id_options selected=$form.comment_id}
					</select>
					<br />
					自由文&nbsp;&nbsp;<input type="text" name="comment" value="{$form.comment}">（自由文の場合のみ）
					<br />
				</div>
				<br />
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
