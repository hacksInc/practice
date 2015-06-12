<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ランキング集計データ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>ランキング集計結果一覧</h2>
			<br>
			<table class="table table-bordered table-condensed">
				<tr>
					<th>ランキングID</th>
					<th>タイトル【サブタイトル】</th>
					<th>開催期間</th>
					<th>開催状態</th>
					<th>最終集計日時</th>
					<th>集計状態</th>
					<th>Action</th>
				</tr>
				{foreach from=$app.masters item="row" key="i"}
				<tr>
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
					<td><!-- 開催状態 -->
						{$row.status}
					</td>
					<td><!-- 最終集計日時 -->
						{$row.last_update}
					</td>
					<td><!-- 集計状態 -->
						{$row.result_status}
					</td>
					<td><!-- 集計結果参照ボタン -->
						{if $row.result_status != '未集計'}
						<form action="view" method="post" class="one-button-only">
							<input type="hidden" name="ranking_id" value="{$row.ranking_id}">
							<input type="submit" value="集計結果を見る" class="btn btn-block">
						</form>
						{/if}
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

</body>
</html>