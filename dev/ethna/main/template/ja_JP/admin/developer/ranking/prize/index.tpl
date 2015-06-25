<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ランキング賞品配布 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>賞品配布設定</h2>
			<h4>&nbsp;『{$app.title}{if empty($app.subtitle) === false}&nbsp;【{$app.subtitle}】{/if}』</h4>
			<br>

			<!-- 追加ボタン -->
			<form action="create/input" method="post">
				<input type="hidden" name="ranking_id" value="{$app.ranking_id}">
				<input type="submit" value="追加" class="btn"> ランキングの賞品の配布設定を行ないます。
			</form>
			<br>

			<!-- ダウンロードボタン -->
			<form action="download" method="post">
				<input type="hidden" name="ranking_id" value="{$app.ranking_id}">
				<input type="submit" value="ダウンロード" class="btn"> 配布設定をCSVファイルでダウンロードします。
			</form>
			<br>

			<!-- アップロードボタン -->
			※追加のみ行なえます。既に設定されているレコードの変更・削除は反映しません。<br>
			※アップロードで登録された設定は、未配布の状態で登録されます。
			<form action="upload/confirm" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="ranking_id" value="{$app.ranking_id}">
				<input type="file" name="csv" size="30" class="file-drop"><br>
				<input type="submit" value="アップロード" class="btn">
			</form>
			<br>

			<!-- 配布ボタン -->
			<form action="distribute/exec" method="post">
				<input type="hidden" name="ranking_id" value="{$app.ranking_id}">
				<input type="submit" value="配布" class="btn distribute-btn"> 未配布の賞品を配布します。
			</form>
			<br>
			<table class="table table-bordered table-condensed">
				<tr>
					<th>Action</th>
					<th>配布先頭順位</th>
					<th>配布末尾順位</th>
					<th>賞品タイプ</th>
					<th>賞品ID</th>
					<th>賞品名</th>
					<th>LV</th>
					<th>配布数</th>
					<th>状態</th>
					<th>Action</th>
				</tr>
				{foreach from=$app.prize item="row" key="i"}
				<tr>
					<td>
						{if $row.status == 0}
						<!-- 修正ボタン -->
						<form action="update/input" method="post" class="one-button-only">
							<input type="hidden" name="id" value="{$row.id}">
							<input type="hidden" name="ranking_id" value="{$row.ranking_id}">
							<input type="submit" value="修正" class="btn btn-block">
						</form>
						{/if}
					</td>
					<td><!-- 配布先頭順位 -->
						{$row.distribute_start}
					</td>
					<td><!-- 配布末尾順位 -->
						{if empty( $row.distribute_end ) === false }
							{$row.distribute_end}
						{else}
							―
						{/if}
					</td>
					<td><!-- 賞品タイプ -->
						{$row.prize_type_name}
					</td>
					<td><!-- 賞品ID -->
						{if empty( $row.prize_id ) === false }
							<!-- 通常アイテムorモンスターの場合のみ -->
							{$row.prize_id}
						{else}
							―
						{/if}
					</td>
					<td><!-- 賞品名 -->
						{if empty( $row.prize_name ) === false }
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
					<td><!-- ステータス -->
						{$row.prize_status_name}
					</td>
					<td><!-- 削除ボタン -->
						{if $row.status == 0}
						<form action="delete/exec" method="post" class="one-button-only">
							<input type="hidden" name="id" value="{$row.id}">
							<input type="hidden" name="ranking_id" value="{$row.ranking_id}">
							<input type="submit" value="削除" class="btn btn-block delete-btn">
						</form>
						{elseif $row.status == 1}
						<form action="abort/exec" method="post" class="one-button-only">
							<input type="hidden" name="id" value="{$row.id}">
							<input type="hidden" name="ranking_id" value="{$row.ranking_id}">
							<input type="submit" value="配布中止" class="btn btn-block abort-btn">
						</form>
						{else}
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

{literal}
<script>
	$(function(){
		$('input.distribute-btn').click(function() {
			return window.confirm("賞品を配布します。よろしいですか？");
		});
	});
	$(function(){
		$('input.delete-btn').click(function() {
			return window.confirm("配布設定を削除します。よろしいですか？");
		});
	});
	$(function(){
		$('input.abort-btn').click(function() {
			return window.confirm("配布を中止します。よろしいですか？");
		});
	});
</script>
{/literal}

</body>
</html>