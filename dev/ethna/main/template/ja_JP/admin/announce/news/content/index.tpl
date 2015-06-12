<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="お知らせ - サイコパス管理ページ"}
<body>
<link href="/psychopass_game/css/admin/announce/text-inverse.css" rel="stylesheet">
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
			<h2>お知らせデータ一覧</h2>
			<div class="text-right">
                {if $app.resource_disabled}
    				<a class="text-right muted">＞ 現在のお知らせテスト表示</a>
                {else}
    				<a href="//{$app.resource_host}/psychopass_game/resource/infoList?lang={$app.lang}{if $app.appver}&appver={$app.appver}{/if}" target="_blank" class="text-right">＞ 現在のお知らせテスト表示</a>
                {/if}
			</div>

			<form action="create/input" method="get">
				<input type="submit" value="追加" class="btn"> 新規お知らせを追加します。
			</form>

			{foreach from=$app.list item="row" key="i"}
				<div class="row-fluid" style="border-top: solid 1px; border-left: solid 1px; border-right: solid 1px;">
					<div class="text-center">
						{if $row.status == "waiting"}
							{if $row.test_flag}
								<span class="news-content-status-flag">表示テスト中</span>
								<form action="flag/exec" method="post">
									<input type="hidden" name="content_id" value="{$row.content_id}">
									<input type="hidden" name="test_flag" value="0">
									<input type="submit" value="表示テスト停止">
								</form>
							{else}
								<span class="news-content-status-waiting">表示予定</span>
{* 表示テストは、クライアント側アプリのWebViewなどとの認証の仕様が定義されるまで、保留
								<form action="flag/exec" method="post">
									<input type="hidden" name="content_id" value="{$row.content_id}">
									<input type="hidden" name="test_flag" value="1">
									<input type="submit" value="表示テスト">
								</form>
*}
							{/if}
						{elseif $row.status == "active"}
							<span class="news-content-status-active">表示中</span>
						{/if}
					</div>

					{if $row.status == "waiting"}
					<div class="text-right">
						<a class="pop-news-content" href="#" data-news-date="{$row.date_disp_short}" data-news-title="{$row.title}" data-news-body="{$row.body}">＞ 表示テスト</a>
					</div>
					{/if}

					<div class="row-fluid">
				        <div class="span1">
							<form action="update/input" method="get">
								<input type="hidden" name="content_id" value="{$row.content_id}">
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
									{form_name name="date_disp"}
								</div>
								<div class="span4">
									{$row.date_disp}
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
						<div class="admin-announce-text-inverse">{$app_ne.list[$i].abridge}</div>
						<div>
							<button type="button" class="btn btn-mini btn-link" data-toggle="collapse" data-target="#abridge{$i}-src">
								ソース表示
							</button>
							<div id="abridge{$i}-src" class="collapse">
								{$app.list[$i].abridge}
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
							<div class="row-fluid">
								<div class="span2">
									{form_name name="date_start"}
								</div>
								<div class="span4">
									{$row.date_start}
								</div>
								<div class="span2">
									{form_name name="date_end"}
								</div>
								<div class="span4">
									{$row.date_end}
								</div>
							</div>
						</div>
				        <div class="span1">
							<form action="create/input" method="get">
								<input type="hidden" name="content_id" value="{$row.content_id}">
								<input type="submit" value="複製" class="btn">
							</form>
						</div>
				        <div class="span1">
							<form action="end/exec" method="post">
								<input type="hidden" name="content_id" value="{$row.content_id}">
								<input type="submit" value="表示&#13;&#10;終了" class="btn end-btn">
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
		$('input.end-btn').click(function() {
			return window.confirm('表示終了しますがよろしいですか？');
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
