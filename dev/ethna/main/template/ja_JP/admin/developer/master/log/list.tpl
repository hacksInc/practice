<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="`$app.table_label` - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">

		<div class="span9">
			<h2>{$app.table_label}</h2>
			{if $form.gacha_id}
				<p>ガチャID {$form.gacha_id}</p>
			{/if}

			<h3>CSV取得</h3>

			ログデータを選択してください。<br>
			{strip}
			<table class="table table-condensed">
				<tr>
					<th>#</th>
					<th>date</th>
					<th>user</th>
					<th>filename</th>
				</tr>
				{foreach from=$app.list item="item" name="loop1"}
					<tr>
						{if $smarty.foreach.loop1.index % 2 == 0}
							<td rowspan="2">{$smarty.foreach.loop1.index/2+1}</td>
							<td rowspan="2">{$item.isodate}</td>
							<td rowspan="2">{$item.user}</td>
						{/if}
						<td>{a href="download?file=`$item.filename`"}{$item.filename}{/a}</td>
					</tr>
				{foreachelse}
					<tr><td colspan="4"><span class="muted">データがありません。</span></td></tr>
				{/foreach}
			</table>
			{/strip}

			<p>
				※ログについては、<br>
				　・{$app.max}件保存可能<br>
				　・古いものから自動で消去<br>
			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
