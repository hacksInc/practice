<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ゲーム制御 - サイコパス管理ページ"}
<body>
{*
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
*}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ゲーム制御</h2>
			<form action="index" method="post" class="form-horizontal">

				<div class="date_dist_start">
					{$app.date_now|date_format:"%H:%M:%S"}<br />
					運用ステータス&nbsp;&nbsp;&nbsp;
					{if $app.status==0}通常運用中{/if}
					{if $app.status==1}通常運用中　－　<b><font color="#0000FF">メンテナンス予定有</font></b>{/if}
					{if $app.status==2}<b><font color="#ff0000">メンテナンス中</font></b>{/if}
					<br />

					メンテナンス開始&nbsp;&nbsp;&nbsp;
					{if $app.status==0}
						<input type="text" name="date_mstart" value="{$app.date_start}" class="jquery-ui-datetimepicker">
						<input type="submit" value="メンテナンス開始" class="btn end-btn" />
						<input type="hidden" name="act" value="1">
					{else}
						<input type="text" name="date_mstart" value="{$app.date_start}" class="jquery-ui-datetimepicker" disabled="true">
						<input type="submit" value="メンテナンス中止" class="btn end-btn" />
						<input type="hidden" name="act" value="0">
					{/if}
					<br />

					メンテナンス終了&nbsp;&nbsp;&nbsp;
					<input type="text" name="date_mend" value="{$app.date_end}" class="jquery-ui-datetimepicker">
					{if $app.status > 0}
						<input type="submit" name="timechange" value="終了時間変更" class="btn end-btn" />
					{/if}
					<input type="checkbox" name="unitsync" value="1" checked>他ユニットも同時に更新
				</div>
			</form>

			<form action="index" method="post" class="form-horizontal">
				メンテ突破フラグ（BTF）設定&nbsp;&nbsp;&nbsp;
				{if $app.btf==0}
					無効<br>
					<input type="hidden" name="btf" value="1">
					<input type="submit" value="BTFを有効にする" class="btn end-btn" />
				{/if}
				{if $app.btf==1}
					有効<br>
					<input type="hidden" name="btf" value="0">
					<input type="submit" value="BTFを無効にする" class="btn end-btn" />
				{/if}
				<input type="checkbox" name="unitsync" value="1" checked>他ユニットも同時に更新
			</form>

			<hr />

			{if count($app.mes)}
				<div class="alert alert-error">
				{foreach from=$app.mes item=mes}
					<div style="color: #000000;">{$mes}</div>
				{/foreach}
				</div>
			{/if}

			<div class="unit_stat">
				各ユニットの運用ステータス<br />
				{foreach from=$app.unit_stat key=uk item=stat}
				<div>
					unit {$uk}:
					{if $stat.status==0}通常運用中{/if}
					{if $stat.status==1}通常運用中　－　<b><font color="#0000FF">メンテナンス予定有</font></b>>{/if}
					{if $stat.status==2}<b><font color="#ff0000">メンテナンス中</font></b>{/if}
					&nbsp;
					{if $stat.btf==0}BTF無効{/if}
					{if $stat.btf==1}<b><font color="#008000">BTF有効</font></b>{/if}
				</div>
				{/foreach}
			</div>

			<div class="text-right">{a href="log/view"}＞ 操作ログ閲覧{/a}</div>

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
			return window.confirm('設定しますがよろしいですか？');
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
