<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="t_ranking_prize - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>賞品配布設定アップロード内容</h2>
			<h4>&nbsp;『{$app.title}{if empty($app.subtitle) === false}&nbsp;【{$app.subtitle}】{/if}』</h4>
			<br>

			<!-- 追加ボタン -->
			<form action="regist" method="post">
				<input type="hidden" name="ranking_id" value="{$app.ranking_id}">
				<input type="hidden" name="csv" value="{$app.csv}">
				<input type="submit" value="追加" class="btn">
			</form>

			<table class="table table-bordered table-condensed">
				<tr>
					<th>配布先頭順位</th>
					<th>配布末尾順位</th>
					<th>賞品タイプ</th>
					<th>賞品ID</th>
					<th>賞品名</th>
					<th>LV</th>
					<th>配布数</th>
				</tr>
				{foreach from=$app.prizes item="row" key="i"}
				<tr>
					<td><!-- 配布先頭順位 -->
						{$row.distribute_start}
					</td>
					<td><!-- 配布末尾順位 -->
						{if empty( $row.distribute_end ) === false}
							{$row.distribute_end}
						{else}
							―
						{/if}
					</td>
					<td><!-- 賞品タイプ -->
						{$row.prize_type_name}
					</td>
					<td><!-- 賞品ID -->
						{if empty( $row.prize_id ) === false}
							<!-- 通常アイテムorモンスターの場合のみ -->
							{$row.prize_id}
						{else}
							―
						{/if}
					</td>
					<td><!-- 賞品名 -->
						{if empty( $row.prize_name ) === false}
							{$row.prize_name}
						{else}
							―
						{/if}
					</td>
					<td><!-- LV -->
						{if $row.prize_type == '2'}
							<!-- モンスターの場合のみ -->
							{$row.lv}
						{else}
							―
						{/if}
					</td>
					<td><!-- 配布数 -->
						{$row.number}
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