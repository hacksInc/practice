<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="イベントのお知らせ - サイコパス管理ページ"}
<body>
<link href="/css/admin/announce/text-inverse.css" rel="stylesheet">
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
			
			<h2>イベントお知らせデータ一覧</h2>
			<div class="text-right">
                {if $app.resource_disabled}
    				<a class="muted">＞ 現在のお知らせテスト表示</a>
                {else}
                    <a href="//{$app.resource_host}/resource/eventinfo?lang=ja&ua=1{if $app.appver}&appver={$app.appver}{/if}" target="_blank" class="text-right">＞ 現在のお知らせテスト表示 [iOS]</a><br>
                    <a href="//{$app.resource_host}/resource/eventinfo?lang=ja&ua=2{if $app.appver}&appver={$app.appver}{/if}" target="_blank" class="text-right">[Android]</a>
                {/if}
			</div>
			
			<form action="create/input" method="get">
				<input type="submit" value="追加" class="btn"> 新規イベントお知らせを追加します。
			</form>
				
			{foreach from=$app.list item="row" key="i"}
				<div class="row-fluid" style="border-top: solid 1px; border-left: solid 1px; border-right: solid 1px;">
					<div class="text-center">
						{if $row.disp_sts == constant("Jm_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_NORMAL")}
							{if $row.status == "waiting"}
								<span class="news-content-status-waiting">表示予定</span>
								<form action="sts/exec" method="post">
									<input type="hidden" name="content_id" value="{$row.content_id}">
									<input type="hidden" name="disp_sts" value="{"Jm_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_TEST"|constant}">
									<input type="submit" value="表示テスト" class="btn sts-btn" disabled>
									<i class="icon-question-sign" data-original-title="表示テストは機能しません。（未実装です）"></i>
								</form>
							{elseif $row.status == "active"}
								<span class="news-content-status-active">表示中</span>
								<form action="sts/exec" method="post">
									<input type="hidden" name="content_id" value="{$row.content_id}">
									<input type="hidden" name="disp_sts" value="{"Jm_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_PAUSE"|constant}">
									<input type="submit" value="表示一時停止" class="btn sts-btn">
								</form>
							{/if}
						{elseif $row.disp_sts == constant("Jm_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_TEST")}
							<span class="news-content-status-flag">表示テスト中</span>
							<form action="sts/exec" method="post">
								<input type="hidden" name="content_id" value="{$row.content_id}">
								<input type="hidden" name="disp_sts" value="{"Jm_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_NORMAL"|constant}">
								<input type="submit" value="表示テスト停止" class="btn sts-btn">
							</form>
						{elseif $row.disp_sts == constant("Jm_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_PAUSE")}
							<span class="news-content-status-flag">表示一時停止</span>
							<form action="sts/exec" method="post">
								<input type="hidden" name="content_id" value="{$row.content_id}">
								<input type="hidden" name="disp_sts" value="{"Jm_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_NORMAL"|constant}">
								<input type="submit" value="表示" class="btn sts-btn">
							</form>
						{/if}
					</div>

					{if ($row.status == "waiting") || ($row.disp_sts != constant("Jm_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_NORMAL"))}
					<div class="text-right">
						<a class="pop-news-content" href="#" data-news-banner="{$row.banner}" data-news-content-id="{$row.content_id}" data-news-body="{$row.body}">＞ 表示テスト</a>
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
								<div class="span6">
								</div>
								<div class="span2">
									{form_name name="ua"}
								</div>
								<div class="span4">
									{$app.form_template.ua.option[$row.ua]}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="priority"}
								</div>
								<div class="span4">
									{$app.form_template.priority.option[$row.priority]}
								</div>
								<div class="span2">
									{form_name name="date_disp"}<i class="icon-question-sign" data-original-title="表示日時はゲーム画面に表示されません。"></i>
								</div>
								<div class="span4">
									{$row.date_disp}
								</div>
							</div>
						</div>
					</div>
					<div class="row-fluid text-center">
						<div class="admin-announce-text-inverse">
						{if $row.banner}
							<img src="image?content_id={$row.content_id}"><br>
						{/if}
						{$app_ne.list[$i].body}
						</div>
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

			<div class="text-right">{a href="log/view"}＞ 操作ログ閲覧{/a}&nbsp;&nbsp;{a href="history?lu=`$form.lu`"}＞ ログ閲覧{/a}</div>
			
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
			return window.confirm('表示終了しますがよろしいですか？');
		});
		
		$('input.sts-btn').click(function() {
			return window.confirm('表示ステータスを変更します。よろしいですか？');
		});
		
		$('a.pop-news-content').popover({
			html: true,
			trigger: 'click',
//			placement: 'left',
			placement: 'bottom',
			content: function(){
				var content = '<div style="text-align: center;">';
				if ($(this).data('news-banner')) {
					content += '<img src="image?content_id=' + $(this).data('news-content-id') + '" />';
				}
				content += $(this).data('news-body') + '</div>';
				
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