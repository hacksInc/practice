<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="プレゼントBOX直接配布 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>プレゼントBOX直接配布　確認画面</h2>
			<form action="exec" method="post" class="form-horizontal">

			<h4>対象ユーザー</h4>
				<input type="hidden" name="ppid_list" value="{$form.ppid_list}">
				{foreach from=$app.pp_ids item=v}
					&nbsp;&nbsp;
					{$v.ppid}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$v.nickname}
					{*{if $v.user_ng == 1}<font color="#ff0000">他ユニットユーザ</font>{/if}*}
					{if $v.user_ng == 1}
						<font color="#ff0000">指定されたユーザーIDはユニットが違います！</font>
					{elseif $v.user_exists== 1}
						<font color="#ff0000">ユーザーが存在しません！</font>
					{elseif $v.user_fmt_ng == 1}
						<font color="#ff0000">ユーザーIDを正しく入力して下さい！</font>
					{/if}
					<br />
				{/foreach}

			<h4>配布内容</h4>
				&nbsp;&nbsp;
				{$app.present_value}
				<input type="hidden" name="present_value" value="{$form.present_value}">
				<input type="hidden" name="present_category" value="{$app.present_category}">
				<input type="hidden" name="num" value="{$form.num}">
				&nbsp;&nbsp;
				{if $app.present_category == 2}
					{$form.num}枚
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					配布ID&nbsp;&nbsp;{$form.item_id}&nbsp;&nbsp;{$app.photo_name}
					{if $app.photo_name==NULL}<font color="#ff0000">フォトが存在しません！</font>{/if}
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					レベル&nbsp;&nbsp;{$form.lv}
					<input type="hidden" name="item_id" value="{$form.item_id}">
					<input type="hidden" name="lv" value="{$form.lv}">
				{elseif $app.present_category == 3}
					{$form.num}ポイント
					<input type="hidden" name="item_id" value="0">
					<input type="hidden" name="lv" value="0">
				{else}
					{$form.num}個
					<input type="hidden" name="item_id" value="0">
					<input type="hidden" name="lv" value="0">
				{/if}
				<br />
				&nbsp;&nbsp;
				コメント&nbsp;&nbsp;{$app.comment_id}
				<input type="hidden" name="comment_id" value="{$form.comment_id}">
				{if $form.comment_id==0}
					<br />
					&nbsp;&nbsp;
					自由文&nbsp;&nbsp;{$form.comment}
					<input type="hidden" name="comment" value="{$form.comment}">
				{else}
					<input type="hidden" name="comment" value="">
				{/if}
				<br />
				<div class="text-center">
					{if $app.present_category == 2 && $app.photo_name==NULL}
						&nbsp;&nbsp;
					{else}
						{if $app.is_regist == false}
							&nbsp;&nbsp;
						{else}
							<input type="submit" value="配布登録" class="btn end-btn" />
						{/if}
					{/if}
				</div>
			</form>
			<form action="input2" method="post" class="form-horizontal">
				<div class="text-center">
					<input type="hidden" name="ppid_list" value="{$form.ppid_list}">
					<input type="hidden" name="target_type" value="{$form.target_type}">
					<input type="hidden" name="present_category" value="{$form.present_category}">
					<input type="hidden" name="num" value="{$form.num}">
					<input type="hidden" name="present_value" value="{$form.present_value}">
					{if $form.present_category == 1006}
						<input type="hidden" name="item_id" value="{$form.item_id}">
						<input type="hidden" name="lv" value="{$form.lv}">
					{else}
						<input type="hidden" name="item_id" value="0">
						<input type="hidden" name="lv" value="1">
					{/if}
					<input type="hidden" name="comment_id" value="{$form.comment_id}">
					{if $form.comment_id==0}
						<input type="hidden" name="comment" value="{$form.comment}">
					{else}
						<input type="hidden" name="comment" value="">
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
{literal}
<script>
	$(function(){
		$('#select-lu').change(function(){
			$('#form-lu').submit();
		});

		$('input.end-btn').click(function() {
			return window.confirm('該当ユーザープレゼントを配布しますがよろしいですか？');
		});
	});
</script>
{/literal}
</body>
</html>
