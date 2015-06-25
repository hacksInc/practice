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
			<h2>プレゼントBOX直接配布　登録画面</h2>
			<form action="confirm" method="post" class="form-horizontal">

			<h4>配布対象ユーザー（改行区切）</h4>
				<div class="trg_user">
					<textarea name="ppid_list" cols=10 rows=3 wrap="hard">{$form.ppid_list}</textarea><br />
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
					&nbsp;&nbsp;&nbsp;&nbsp;レベル<input type="text" name="lv" value="{$form.lv}">（配布内容がフォトの場合のみ、マスタの初期バッジ数に追加する値）
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
