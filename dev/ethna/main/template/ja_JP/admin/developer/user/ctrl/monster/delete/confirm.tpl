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
			<h2>ユーザ制御　所持モンスター一括削除</h2>

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
			<table border="0">
				<tr>
					<td align="center">　ユーザモンスターID　</td>
					<td align="center">　モンスターID　</td>
					<td align="center">　モンスター名　</td>
					<td align="center">　LV　</td>
					<td align="center">　経験値　</td>
					<td align="center">　HP補正値　</td>
					<td align="center">　攻撃力補正値　</td>
					<td align="center">　回復力補正値　</td>
					<td align="center">　スキルレベル　</td>
					<td align="center">　入手日時　</td>
				</tr>
				{foreach from=$app.list item="row"}
				<tr>
					<td align="center">{$row.user_monster_id}</td>
					<td align="center">{$row.monster_id}</td>
					<td align="center">{$row.name}</td>
					<td align="center">{$row.lv}</td>
					<td align="center">{$row.exp}</td>
					<td align="center">{$row.hp_plus}</td>
					<td align="center">{$row.attack_plus}</td>
					<td align="center">{$row.heal_plus}</td>
					<td align="center">{$row.skill_lv}</td>
					<td align="center">{$row.date_created}</td>
				</tr>
				{/foreach}
			</table>
				<form action="exec" method="post">
					<input type="hidden" name="user_id" value="{$form.user_id}">
					{foreach from=$form.user_monster_ids item="user_monster_id"}
						<input type="hidden" name="user_monster_ids[]" value="{$user_monster_id}">
					{/foreach}
					
					<input type="submit" value="削除する" class="btn" />
				</form>
			
			<p>
				<br />
				{a href="../../usermonster?id=`$form.user_id`"}戻る{/a}
			</p>
			
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
