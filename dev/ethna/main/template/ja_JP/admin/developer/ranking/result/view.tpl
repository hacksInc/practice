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
			<h2>ランキング集計結果一覧</h2>
			<h4>&nbsp;『{$app.title}{if empty($app.subtitle) === false}&nbsp;【{$app.subtitle}】{/if}』</h4>
			<br>

			<div class="span6">最終集計日時&nbsp;：&nbsp;{$app.last_update}</div>
			<div class="span6">レコード数&nbsp;：&nbsp;{$app.record_count}</div>

			<table class="table table-bordered table-condensed">
				<tr>
					<th>INDEX</th>
					<th>順位</th>
					<th>ユーザーID</th>
					<th>ユーザー名</th>
					<th>スコア</th>
				</tr>
				{foreach from=$app.ranking_list item="row" key="i"}
				<tr>
					<td><!-- INDEX -->
						{$row.buffer_record_id}
					</td>
					<td><!-- 順位 -->
						{$row.rank}
					</td>
					<td><!-- ユーザーID -->
						{$row.user_id}
					</td>
					<td><!-- ユーザー名 -->
						{$row.name}
					</td>
					<td><!-- スコア -->
						{$row.score}
					</td>
				</tr>
				{/foreach}
			</table>
			{a href="index"}一覧へ戻る{/a}
		</div><!--/span-->
	</div><!--/row-->
	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}

</body>
</html>