<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="メインバナー - サイコパス管理ページ"}
<body>
{literal}
<style type="text/css">
	.content-status-waiting {
		font-weight: bold;
	}
	.content-status-flag {
		font-weight: bold;
		color: blue;
	}
	.content-status-active {
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
			<h2>メインバナー一覧<i class="icon-question-sign" data-original-title="“公開中”と“公開予定”のメッセージが、Priorityが高い順に表示されます。“公開終了”したものについては「ログ閲覧」画面へ移行します。"></i></h2>
			<form action="create/input" method="get">
				<input type="submit" value="追加" class="btn"> 新規メインバナーの登録を行ないます。
			</form>

			{foreach from=$app.list item="row" key="i"}
				<div class="row-fluid" style="border-top: solid 1px; border-left: solid 1px; border-right: solid 1px;">
					<div class="text-center">
						{if $row.disp_sts == constant("Pp_AdminNewsManager::HOME_BANNER_DISP_STS_NORMAL")}
							{if $row.status == "waiting"}
								<span class="content-status-waiting">公開予定</span>
								<form action="sts/exec" method="post">
									<input type="hidden" name="hbanner_id" value="{$row.hbanner_id}">
									<input type="hidden" name="disp_sts" value="{"Pp_AdminNewsManager::HOME_BANNER_DISP_STS_TEST"|constant}">
									<input type="submit" value="テスト公開" class="btn sts-btn" disabled>
									<i class="icon-question-sign" data-original-title="テスト公開は機能しません。（未実装です）"></i>
								</form>
							{elseif $row.status == "active"}
								<span class="content-status-active">公開中</span>
								<form action="sts/exec" method="post">
									<input type="hidden" name="hbanner_id" value="{$row.hbanner_id}">
									<input type="hidden" name="disp_sts" value="{"Pp_AdminNewsManager::HOME_BANNER_DISP_STS_PAUSE"|constant}">
									<input type="submit" value="公開一時停止" class="btn sts-btn">
								</form>
							{/if}
						{elseif $row.disp_sts == constant("Pp_AdminNewsManager::HOME_BANNER_DISP_STS_TEST")}
							<span class="content-status-flag">テスト公開中</span>
							<form action="sts/exec" method="post">
								<input type="hidden" name="hbanner_id" value="{$row.hbanner_id}">
								<input type="hidden" name="disp_sts" value="{"Pp_AdminNewsManager::HOME_BANNER_DISP_STS_NORMAL"|constant}">
								<input type="submit" value="テスト公開停止" class="btn sts-btn">
							</form>
						{elseif $row.disp_sts == constant("Pp_AdminNewsManager::HOME_BANNER_DISP_STS_PAUSE")}
							<span class="content-status-flag">公開一時停止</span>
							<form action="sts/exec" method="post">
								<input type="hidden" name="hbanner_id" value="{$row.hbanner_id}">
								<input type="hidden" name="disp_sts" value="{"Pp_AdminNewsManager::HOME_BANNER_DISP_STS_NORMAL"|constant}">
								<input type="submit" value="公開" class="btn sts-btn">
							</form>
						{/if}
					</div>

					<div class="row-fluid">
				        <div class="span1">
							<form action="update/input" method="get">
								<input type="hidden" name="hbanner_id" value="{$row.hbanner_id}">
								<input type="submit" value="修正" class="btn">
							</form>
						</div>
						<div class="span11">
							<div class="row-fluid">
								<div class="span2">
									{form_name name="hbanner_id"}
								</div>
								<div class="span4">
									{$row.hbanner_id}
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
									{form_name name="img_id"}
								</div>
								<div class="span4">
									{$row.img_id}
								</div>
								<div class="span2">
									{form_name name="type"}
								</div>
								<div class="span4">
									{$app.form_template.type.option[$row.type]}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span6">
									&nbsp;
								</div>
								<div class="span2">
									{form_name name="pri"}
								</div>
								<div class="span4">
									{$app.form_template.pri.option[$row.pri]}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="memo"}
								</div>
								<div class="span10">
									{$row.memo}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="url_ja"}
								</div>
								<div class="span10">
									{$row.url_ja}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="banner_attribute"}
								</div>
								<div class="span10">
									{$row.banner_attribute}
								</div>
							</div>
						</div>
					</div>
					<div class="row-fluid text-center">
						{if $row.type!=11}
							<img src="image?img_id={$row.img_id}&dummy={$app.mtime}">
						{else}
							画像なし
						{/if}
					</div>
					<div class="row-fluid">
						&nbsp;
					</div>
					<div class="row-fluid">
						<div class="span11">
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
							<form action="end/exec" method="post">
								<input type="hidden" name="hbanner_id" value="{$row.hbanner_id}">
								<input type="submit" value="公開&#13;&#10;終了" class="btn end-btn">
							</form>
						</div>
					</div>
				</div>
			{/foreach}
			<div class="row-fluid" style="border-top: solid 1px;">
				&nbsp;
			</div>

			<div class="text-right">{a href="log/view"}＞ 操作ログ閲覧{/a}&nbsp;&nbsp;{a href="history"}＞ ログ閲覧{/a}</div>

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
			return window.confirm('公開終了しますがよろしいですか？');
		});

		$('input.sts-btn').click(function() {
			return window.confirm('表示ステータスを変更します。よろしいですか？');
		});
	});
</script>
{/literal}
</body>
</html>
