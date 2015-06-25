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
			<h2>ユーザ制御　チーム情報</h2>

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
			
			<font color="#ff0000">{$app.err_msg}<br /></font>
			
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
					<form action="usermonsterteamconf" method="post">
					<input type="hidden" name="id" value="{$app.base.user_id}">
					<input type="hidden" name="team_id" value="{$v.team_id}">
				{/if}
				<tr>
					<td align="center">{$k}</td>
					<td align="center">
						<select name="leader_flag{$v.position}" style="width:50px;">
							<option value="1"{if $v.leader==1} selected{/if}>★</option>
							<option value="0"{if $v.leader==0} selected{/if}>　</option>
						</select>
					</td>
					<td align="center"><input type="text" style="width:100px;" name="user_monster_id{$v.position}" value="{$v.user_monster_id}"></td>
					<td align="center">{$v.monster_id}</td>
					<td align="center">{$v.name}</td>
					<td align="center">{$v.cost}</td>
				</tr>
				{if $v.position==5}
					<tr>
					<td colspan=6 align="center">
						<input type="submit" name="update" value="変更確認" class="btn" />
					</td>
					</tr>
					</form>
					</table><br />
				{/if}
			{/foreach}
			
			<br />
			
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
