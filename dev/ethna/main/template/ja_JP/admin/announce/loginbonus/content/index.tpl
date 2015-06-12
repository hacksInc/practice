<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ログインボーナス管理 - サイコパス管理ページ"}
<body>
{literal}
<style type="text/css">
	.news-content-status-waiting {
		font-weight: bold;
	}
	.news-content-status-flag {
		font-weight: bold;
		color: blue;
	}
	.news-content-status-active {
		font-weight: bold;
		color: red;
	}
</style>
{/literal}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<form action="index" method="post" class="text-right" id="form-lu">
				{form_input name="lu" id="select-lu"}
			</form>
			
			<h2>ログインボーナス　管理画面</h2>
			<form action="create/input" method="post">
				<input type="hidden" name="max_id" value="{$app.max_id}">
				<input type="submit" value="新規設定" class="btn"> 新しいログインボーナスを設定します
			</form>
			
			{foreach from=$app.loginbonus item="row" key="i"}
				{if $app.loginbonus[$i].login_bonus_id >= $app.lb.login_bonus_id}
				<div class="row-fluid" style="border-top: solid 1px; border-left: solid 1px; border-right: solid 1px;">
					<div class="row-fluid">
						<div class="span1">
							<form action="update/input" method="post">
								<input type="hidden" name="id" value="{$app.loginbonus[$i].login_bonus_id}">
								<input type="submit" value="修正" class="btn">
							</form>
							{if $app.loginbonus[$i].login_bonus_id == $app.lb.login_bonus_id}
								<font color="#ff0000"><center>公開中</center></font>
							{/if}
						</div>
						<div class="span4">
							ログインボーナスID：{$app.loginbonus[$i].login_bonus_id}
							<br />
							名称：{$app.loginbonus[$i].name}
							<br />
							開始日：{$app.loginbonus[$i].date_start}
							<br />
							終了日：{$app.loginbonus[$i].date_end}
							<br />
						</div>
						<div class="span4">
							登録：{$app.loginbonus[$i].account_reg}{if $app.loginbonus[$i].account_reg==NULL}????????{/if}
							<br />
							登録日時：{$app.loginbonus[$i].date_created}
							<br />
							更新：{$app.loginbonus[$i].account_upd}{if $app.loginbonus[$i].account_upd==NULL}????????{/if}
							<br />
							更新日時：{$app.loginbonus[$i].date_modified}
							<br />
						</div>
						<div class="span1">
							<br />
							<br />
							<form action="create/duplicate" method="post">
								<input type="hidden" name="max_id" value="{$app.max_id}">
								<input type="hidden" name="id" value="{$row.login_bonus_id}">
								<input type="submit" value="複製" class="btn">
							</form>
						</div>
						<div class="span1">
							<br />
							<br />
							<form action="end/exec" method="post">
								<input type="hidden" name="id" value="{$row.login_bonus_id}">
								<input type="submit" value="停止" class="btn end-btn">
							</form>
						</div>
					</div>
					{*
					<div class="row-fluid">
						<div class="span1">
							&nbsp;<br />
						</div>
						<div class="span4">
							&nbsp;<br />
						</div>
						<div class="span4">
							&nbsp;<br />
						</div>
						<div class="span1">
						</div>
					</div>
					*}
				</div>
				{/if}
			{/foreach}
			<div class="row-fluid" style="border-top: solid 1px;">
				&nbsp;
			</div>

			<div class="text-right">{a href="history"}＞ ログ閲覧{/a}</div>
			
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
		
		$('input.end-btn').click(function() {
			return window.confirm('昨日付で配布を終了しますがよろしいですか？');
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