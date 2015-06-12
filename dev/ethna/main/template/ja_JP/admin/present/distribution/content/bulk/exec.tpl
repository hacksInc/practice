<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="プレゼントBOX配布管理 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>プレゼントBOX配布　完了画面</h2>
			<p>
				以下のユーザへ配布しました
			</p>
				{foreach from=$app.pp_ids item=v}
					&nbsp;&nbsp;
					{$v.ppid}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$v.nickname}
					{if $v.ng == 1}<font color="#ff0000">他ユニットユーザ</font>{/if}
					<br />
				{/foreach}

			<br />
			<p>
				配布内容
			</p>
				&nbsp;&nbsp;
				{$app.present_value}
				&nbsp;&nbsp;
				{if $form.present_category == 2}
					{$form.num}枚
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					配布ID&nbsp;&nbsp;{$form.item_id}&nbsp;&nbsp;{$app.photo_name}
					{if $app.photo_name==NULL}<font color="#ff0000">フォトが存在しません！</font>{/if}
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					レベル&nbsp;&nbsp;{$form.lv}
				{elseif $form.present_category == 3}
					{$form.num}ポイント
				{else}
					{$form.num}個
				{/if}
				<br />
				&nbsp;&nbsp;
				コメント&nbsp;&nbsp;{$app.comment_id}
				{if $form.comment_id==0}
					<br />
					&nbsp;&nbsp;
					自由文&nbsp;&nbsp;{$form.comment}
				{/if}

			<br />
			<br />
			<p>
				{a href="input"}戻る{/a}
 			</p>
       </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
