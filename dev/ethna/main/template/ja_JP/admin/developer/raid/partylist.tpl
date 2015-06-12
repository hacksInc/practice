<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="パーティ一覧 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>パーティ一覧(最新100件)</h2>
			<br />
			{*
			ステータス：{if $form.cond==0}全て{/if}{if $form.cond==1}準備中{/if}{if $form.cond==2}出撃中{/if}{if $form.cond==3}解散{/if}<br />
			*}
			<form action="partylist" method="post" class="text-left">
				ステータス
				<select name="cond">
					<option value="0" {if $form.cond==0}selected{/if}>  全て</option>
					<option value="1" {if $form.cond==1}selected{/if}>1:準備中</option>
					<option value="2" {if $form.cond==2}selected{/if}>2:出撃中</option>
					<option value="3" {if $form.cond==3}selected{/if}>3:解散</option>
				</select>
				&nbsp;
				<input type="submit" value="フィルタ設定" class="btn">
			</form>
			<table border=1>
				<tr>
					<td>no</td>
					<td>party_id</td>
					<td>create_user_id</td>
					<td>master_user_id</td>
					<td>dungeon_id</td>
					<td>difficulty</td>
					<td>dungeon_lv</td>
					<td>member_num</td>
					<td>member_limit</td>
					<td>member_max</td>
					<td>status</td>
					<td>force_elimination</td>
					<td>play_style</td>
					<td>entry_passwd</td>
					<td>sally</td>
					<td>message_id</td>
					<td>date_created</td>
					<td>date_modified</td>
				</tr>
			{foreach from=$app.list key="key" item="item"}
				<tr>
					<td>{$key+1}</td>
					<td>{$item.party_id}</td>
					<td>{$item.create_user_id}</td>
					<td>{$item.master_user_id}</td>
					<td>{$item.dungeon_id}</td>
					<td>{$item.difficulty}</td>
					<td>{$item.dungeon_lv}</td>
					<td>{$item.member_num}</td>
					<td>{$item.member_limit}</td>
					<td>{$item.member_max}</td>
					<td>{$item.status}</td>
					<td>{$item.force_elimination}</td>
					<td>{$item.play_style}</td>
					<td>{$item.entry_passwd}</td>
					<td>{$item.sally}</td>
					<td>{$item.message_id}</td>
					<td>{$item.date_created}</td>
					<td>{$item.date_modified}</td>
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
