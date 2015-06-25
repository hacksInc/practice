<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ制御 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>ユーザ制御　バッジ素材追加完了</h2>

			<table border="0" cellpadding="4">
				<tr>
					<td>ユーザID</td>
					<td>{$form.id}</td>
				</tr>
				<tr>
					<td>ニックネーム</td>
					<td>{$app.base.name}</td>
				</tr>
			</table>
			
			<br />
			{if $app.material_add_cnt > 0}
				以下のバッジ素材を追加しました<br />
				{foreach from=$app.material_add key=k item=v}
					{$v.id}：{$v.name}<br />
				{/foreach}
			{/if}
			<br />
			{if $app.material_err_cnt > 0}
				以下のバッジ素材IDは存在しないため追加できませんでした<br />
				{foreach from=$app.material_err key=k item=v}
					{$v}<br />
				{/foreach}
			{/if}
			
			<p>
				<br />
				{a href="userbadgematerial?id=`$form.id`&table=`$form.table`"}戻る{/a}
			</p>
			
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
