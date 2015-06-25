<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ閲覧 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>ユーザデータ　所持モンスター情報</h2>

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
					<td>モンスター所持数</td>
					<td>{$app.monster_cnt}／{$app.base.monster_box_max}</td>
				</tr>
			</table>
			
			<br />
			<table border="0">
				<tr>
					<td align="center">　チーム　</td>
					<td align="center">　リーダー　</td>
					<td align="center">　ユーザモンスターID　</td>
					<td align="center">　モンスターID　</td>
					<td align="center">　モンスター名　</td>
					<td align="center">　LV　</td>
					<td align="center">　経験値　</td>
					<td align="center">　HP補正値　</td>
					<td align="center">　攻撃力補正値　</td>
					<td align="center">　回復力補正値　</td>
					<td align="center">　スキルレベル　</td>
					<td align="center">　コスト　</td>
					<td align="center">　バッジ枠数　</td>
					<td align="center">　装着バッジ　</td>
					<td align="center">　入手日時　</td>
				</tr>
				{foreach from=$app.monster_team key=k item=v}
				<tr>
					<td align="center">{$k}</td>
					<td align="center">{if $v.leader==1}★{/if}</td>
					<td align="center">{$v.user_monster_id}</td>
					<td align="center">{$v.monster_id}</td>
					<td align="center">{$v.name}</td>
					<td align="center">{$v.lv}</td>
					<td align="center">{$v.exp}</td>
					<td align="center">{$v.hp_plus}</td>
					<td align="center">{$v.attack_plus}</td>
					<td align="center">{$v.heal_plus}</td>
					<td align="center">{$v.skill_lv}</td>
					<td align="center">{$v.cost}</td>
					<td align="center">{$v.badge_num}</td>
					<td align="center">{$v.badges}</td>
					<td align="center">{$v.date_created}</td>
				</tr>
				{/foreach}
			</table>
			
			<br />
			
			所持モンスター一覧　※チーム非所属のみ<br />
			<table border="0">
				<tr>
					<td align="center">　No.　</td>
					<td align="center">　ユーザモンスターID　</td>
					<td align="center">　モンスターID　</td>
					<td align="center">　モンスター名　</td>
					<td align="center">　LV　</td>
					<td align="center">　経験値　</td>
					<td align="center">　HP補正値　</td>
					<td align="center">　攻撃力補正値　</td>
					<td align="center">　回復力補正値　</td>
					<td align="center">　スキルレベル　</td>
					<td align="center">　コスト　</td>
					<td align="center">　バッジ枠数　</td>
					<td align="center">　装着バッジ　</td>
					<td align="center">　入手日時　</td>
				</tr>
				{foreach from=$app.monster_free key=k item=v}
				<tr>
					<td align="center">{$k+1}</td>
					<td align="center">{$v.user_monster_id}</td>
					<td align="center">{$v.monster_id}</td>
					<td align="center">{$v.name}</td>
					<td align="center">{$v.lv}</td>
					<td align="center">{$v.exp}</td>
					<td align="center">{$v.hp_plus}</td>
					<td align="center">{$v.attack_plus}</td>
					<td align="center">{$v.heal_plus}</td>
					<td align="center">{$v.skill_lv}</td>
					<td align="center">{$v.cost}</td>
					<td align="center">{$v.badge_num}</td>
					<td align="center">{$v.badges}</td>
					<td align="center">{$v.date_created}</td>
				</tr>
				{/foreach}
			</table>
			
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
