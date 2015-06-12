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
			<h2>ユーザ制御　所持モンスター情報変更</h2>

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
				<tr>
					<td>所属チーム</td>
					<td>
						{foreach from=$app.belong_team key=k item=v}
							{$v}　
						{/foreach}
					</td>
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
					<td align="center">　バッジ枠数　</td>
					<td align="center">　装着バッジ　</td>
					<td align="center">　入手日時　</td>
				</tr>
				<tr>
					<td align="center">{$app.monster_data.user_monster_id}</td>
					<td align="center">{$app.monster_data.monster_id}</td>
					<td align="center">{$app.monster_data.name}</td>
					<td align="center">{$form.lv}</td>
					<td align="center">{$form.exp}</td>
					<td align="center">{$form.hp_plus}</td>
					<td align="center">{$form.attack_plus}</td>
					<td align="center">{$form.heal_plus}</td>
					<td align="center">{$form.skill_lv}</td>
					<td align="center">{$form.badge_num}</td>
					<td align="center">{$form.badges}</td>
					<td align="center">{$app.monster_data.date_created}</td>
				</tr>
			</table>
				<form action="usermonsterchg" method="get">
					<input type="hidden" name="id" value="{$form.id}">
					<input type="hidden" name="user_monster_id" value="{$form.user_monster_id}">
					<input type="hidden" name="lv" value="{$form.lv}">
					<input type="hidden" name="exp" value="{$form.exp}">
					<input type="hidden" name="hp_plus" value="{$form.hp_plus}">
					<input type="hidden" name="attack_plus" value="{$form.attack_plus}">
					<input type="hidden" name="heal_plus" value="{$form.heal_plus}">
					<input type="hidden" name="skill_lv" value="{$form.skill_lv}">
					<input type="hidden" name="badge_num" value="{$form.badge_num}">
					<input type="hidden" name="badges" value="{$form.badges}">
					<input type="hidden" name="func" value="{$app.func}">
					{if $app.leader_flag==1 && $app.func=='delete'}
						<font color="#ff0000">チームリーダーのモンスターは削除できません</font><br />
					{else}
						{if $form.user_monster_id > 0}
							<input type="submit" value="{if $app.func=='update'}更新{else}削除{/if}する" class="btn" />
						{/if}
					{/if}
				</form>
			
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
