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
			<h2>ユーザ制御　チーム情報確認</h2>

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
					<td>チームコスト</td>
					<td>{$app.base.team_cost}</td>
				</tr>
				<tr>
					<td>アクティブチームID</td>
					<td>{$app.base.active_team_id}</td>
				</tr>
				<tr>
					<td>モンスター所持数</td>
					<td>{$app.monster_cnt}／{$app.base.monster_box_max}</td>
				</tr>
			</table>
			
			<br />
			{foreach from=$app.monster_team key=k item=v}
				{if $v.position==1}
					<table border="1">
					<tr>
						<td align="center">　チーム　</td>
						<td align="center">　リーダー　</td>
						<td align="center">　ユーザモンスターID　</td>
						<td align="center">　モンスターID　</td>
						<td align="center">　モンスター名　</td>
						<td align="center">　コスト　</td>
					</tr>
				{/if}
				<tr>
					<td align="center">{$k}</td>
					<td align="center">
						{if $v.leader==1}★{else}　{/if}
					</td>
					<td align="center">{$v.user_monster_id}</td>
					<td align="center">{$v.monster_id}</td>
					<td align="center">{$v.name}</td>
					<td align="center">{$v.cost}</td>
				</tr>
				{if $v.position==5}
					</table><br />
				{/if}
			{/foreach}
			
			以上の内容で変更しました<br />
			
			<p>
				<br />
				{a href="list?by=id&id=`$form.id`"}戻る{/a}
			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
