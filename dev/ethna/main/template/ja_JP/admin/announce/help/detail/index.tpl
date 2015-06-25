<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ヘルプ詳細文 - サイコパス管理ページ"}
<body>
<link href="/psychopass_game/css/admin/announce/text-inverse.css" rel="stylesheet">
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ヘルプ詳細文データ一覧</h2>
			<form action="create/input" method="get">
				<input type="submit" value="追加" class="btn"> 新規ヘルプ詳細文を追加します。
			</form>

			{foreach from=$app.list item="row" key="i"}
				<div class="row-fluid" style="border-top: solid 1px; border-left: solid 1px; border-right: solid 1px;">
					<div class="row-fluid">
				        <div class="span1">
							<form action="update/input" method="get">
								<input type="hidden" name="help_id" value="{$row.help_id}">
								<input type="submit" value="修正" class="btn">
							</form>
						</div>
						<div class="span11">
							<div class="row-fluid">
								<div class="span2">
									{form_name name="priority"}
								</div>
								<div class="span4">
									{$app.form_template.priority.option[$row.priority]}
								</div>
								<div class="span2">
									{form_name name="category_id"}
								</div>
								<div class="span4">
									{$app.category_list[$row.category_id]}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="title"}
								</div>
								<div class="span10">
									<div class="admin-announce-text-inverse">{$app_ne.list[$i].title}</div>
									<div>
										<button type="button" class="btn btn-mini btn-link" data-toggle="collapse" data-target="#title{$i}-src">
											ソース表示
										</button>
										<div id="title{$i}-src" class="collapse">
											{$app.list[$i].title}
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="admin-announce-text-inverse">{$app_ne.list[$i].body}</div>
						<div>
							<button type="button" class="btn btn-mini btn-link" data-toggle="collapse" data-target="#body{$i}-src">
								ソース表示
							</button>
							<div id="body{$i}-src" class="collapse">
								{$app.list[$i].body}
							</div>
						</div>
					</div>
					<div class="row-fluid">
						&nbsp;
					</div>
					<div class="row-fluid">
						<div class="span10">
						</div>
				        <div class="span1">
							<form action="create/input" method="get">
								<input type="hidden" name="help_id" value="{$row.help_id}">
								<input type="submit" value="複製" class="btn">
							</form>
						</div>
				        <div class="span1">
							<form action="delete/exec" method="post">
								<input type="hidden" name="help_id" value="{$row.help_id}">
								<input type="submit" value="削除" class="btn delete-btn">
							</form>
						</div>
					</div>
				</div>
			{/foreach}
			<div class="row-fluid" style="border-top: solid 1px;">
				&nbsp;
			</div>

			<div class="text-right">{a href="log/view"}＞ 操作ログ閲覧{/a}&nbsp;&nbsp;{a href="history?lu=ja1"}＞ ログ閲覧{/a}</div>

        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{literal}
<script>
	$(function(){
		$('input.delete-btn').click(function() {
			return window.confirm('削除しますがよろしいですか？');
		});

		$('a.pop-help-detail').popover({
			html: true,
			trigger: 'click',
			placement: 'left',
			detail: function(){
				var detail = $(this).data('help-date') + ' '
				            + $(this).data('help-title') + '<br />'
				            + $(this).data('help-body');
				return detail;
			}
		});

		$('a.pop-help-detail').click(function(){
			return false;
		});
	});
</script>
{/literal}
</body>
</html>
