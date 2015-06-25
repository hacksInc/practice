<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ランキング管理データ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>ランキング一覧</h2>
			<form action="create/input" method="post">
				<input type="submit" value="追加" class="btn"> 新規ランキングを準備する為、ランキングマスターの登録を行ないます。
			</form>
			
			<table class="table table-bordered table-condensed">
				<tr>
					<th>Action</th>
					<th>Action</th>
					<th>ランキングID</th>
					<th>タイトル【サブタイトル】</th>
					<th>開催期間</th>
					<th>状態</th>
					<th>Action</th>
				</tr>
				{foreach from=$app.masters item="row" key="i"}
				<tr>
					<td><!-- 修正ボタン -->
						<form action="update/input" method="post" class="one-button-only">
							<input type="hidden" name="ranking_id" value="{$row.ranking_id}">
							<input type="submit" value="修正" class="btn btn-block">
						</form>
					</td>
					<td><!-- プレゼント配布ボタン -->
						<form action="prize/index" method="post" class="one-button-only">
							<input type="hidden" name="ranking_id" value="{$row.ranking_id}">
							<input type="submit" value="賞品配布" class="btn btn-block">
						</form>
					</td>
					<td><!-- ランキングID -->
						{$row.ranking_id}
					</td>
					<td><!-- タイトル＆サブタイトル -->
						{$row.title}
						{if empty( $row.subtitle ) === false }
							【{$row.subtitle}】
						{/if}
					</td>
					<td><!-- 開催期間 -->
						{$row.date_start} ～ {$row.date_end}
					</td>
					<td><!-- 状態 -->
						{$row.status}
					</td>
					<td><!-- 削除ボタン -->
						<form action="delete/exec" method="post" class="one-button-only">
							<input type="hidden" name="ranking_id" value="{$row.ranking_id}">
							<input type="submit" value="削除" class="btn btn-block delete-btn">
						</form>
					</td>
				</tr>
				{/foreach}
			</table>
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
			return window.confirm("マスタ、管理情報、集計データを削除します。よろしいですか？");
		});
	});
</script>
{/literal}

</body>
</html>