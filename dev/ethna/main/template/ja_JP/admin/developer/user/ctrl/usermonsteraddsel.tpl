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
			<h2>ユーザ制御　全モンスター追加完了</h2>

			<table border="0" cellpadding="4">
				<tr>
					<td>ユーザID</td>
					<td>{$app.base.user_id}</td>
				</tr>
				<tr>
					<td>ニックネーム</td>
					<td>{$app.base.name}</td>
				</tr>
				<tr>
					<td>モンスター所持数</td>
					<td>{$app.monster_cnt}／{$app.base.monster_box_max}</td>
				</tr>
			</table>
			
			<br />
			{if $app.monster_add_cnt > 0}
				以下のモンスターを追加しました<br />
				{foreach from=$app.monster_add key=k item=v}
					{$v.id}：{$v.name}<br />
				{/foreach}
			{/if}
			<br />
			{if $app.monster_err_cnt > 0}
				以下のモンスターIDは存在しないため追加できませんでした<br />
				{foreach from=$app.monster_err key=k item=v}
					{$v}<br />
				{/foreach}
			{/if}
			
			<p>
				<br />
				{a href="usermonster?id=`$form.id`"}戻る{/a}
			</p>
			
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
