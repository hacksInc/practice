<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="`$app.table_label` - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>{$app.table_label} ({$form.table})</h2>
			<h3>全削除 (ユニット{$app.unit})</h3>
			<form name="rcnf" action="exec" method="POST">
				本当によろしいですか？<br />
				<br />
				<input id="agree" type="checkbox" name="agree" value="1">はい、私は決して後悔いたしません<br />
				<br />
				確認パスワード<br />
				<input type="password" name="confpass" value="" /><br />
				<input type="submit" value="実行" class="btn conf-btn">
				<input type="hidden" name="table" value="{$form.table}">
			</form>
			
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
		
		$('input.conf-btn').click(function() {
			return window.confirm('全削除してしまいますが本当によろしいですか？');
		});
		
		$('a.pop-news-content').popover({
			html: true,
			trigger: 'click',
			placement: 'left',
			content: function(){
				var content = $(this).data('news-date') + ' '
				            + $(this).data('news-title') + '<br />'
				            + $(this).data('news-body');
				return content;
			}
		});
		
		$('a.pop-news-content').click(function(){
			return false;
		});
	});
</script>
{/literal}
</body>
</html>
