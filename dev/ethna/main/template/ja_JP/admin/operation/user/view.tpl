<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザーIDの検索 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			ユーザーID：{$app.base.user_id}<br />
			({$smarty.server.REQUEST_TIME|date_format:"%Y-%m-%d %H:%M:%S"}現在)
			<h3>base</h3>
			<table border="1">
				<tr>
					<th>user_id</th><th>name</th><th>rank</th><th>stamina</th><th>date_stamina_update</th><th>exp</th><th>medal</th><th>gold</th><th>login_date</th><th>total_login</th><th>serial_login</th><th>daily_login</th><th>team_cost</th><th>stamina_max</th><th>friend_max</th><th>friend_expand</th><th>charge_sec</th><th>monster_box_max</th><th>bring_cnt</th><th>bring_total</th><th>active_team_id</th><th>last_user_mail_id</th><th>gacha_point</th><th>gacha_point_total</th><th>tutorial</th><th>app_id</th><th>puid</th><th>account</th><th>cave_id</th><th>login_rand</th><th>date_created</th><th>date_modified</th>
				</tr>	
				<tr>
					<td>{$app.base.user_id}</td><td>{$app.base.name}</td><td align="right">{$app.base.rank}</td><td align="right">{$app.base.stamina}</td><td>{$app.base.date_stamina_update}</td><td align="right">{$app.base.exp}</td><td align="right">{$app.base.medal}</td><td align="right">{$app.base.gold}</td><td>{$app.base.login_date}</td><td align="right">{$app.base.total_login}</td><td align="right">{$app.base.serial_login}</td><td align="right">{$app.base.daily_login}</td><td align="right">{$app.base.team_cost}</td><td align="right">{$app.base.stamina_max}</td><td align="right">{$app.base.friend_max}</td><td align="right">{$app.base.friend_expand}</td><td align="right">{$app.base.charge_sec}</td><td align="right">{$app.base.monster_box_max}</td><td align="right">{$app.base.bring_cnt}</td><td align="right">{$app.base.bring_total}</td><td align="right">{$app.base.active_team_id}</td><td>{$app.base.last_user_mail_id}</td><td align="right">{$app.base.gacha_point}</td><td align="right">{$app.base.gacha_point_total}</td><td>{$app.base.tutorial}</td><td>{$app.base.app_id}</td><td>{$app.base.puid}</td><td>{$app.base.account}</td><td>{$app.base.cave_id}</td><td>{$app.base.login_rand}</td><td>{$app.base.date_created}</td><td>{$app.base.date_modified}</td>
				</tr>	
			</table>
			<br />
			<h3>friend_list</h3>
			<h4>STATUS_REQUEST_S</h4>
				<table border="1">
					<tr>
						<th>user_id</th><th>friend_id</th><th>status</th><th>date_bring</th><th>date_send_ticket</th><th>date_created</th><th>date_modified</th>
					</tr>
					{foreach from=$app.friend_list.1 item="row"}
					<tr>
						<td>{$row.user_id}</td><td>{$row.friend_id}</td><td align="right">{$row.status}</td><td>{$row.date_bring}</td><td>{$row.date_send_ticket}</td><td>{$row.date_created}</td><td>{$row.date_modified}</td>
					</tr>
					{/foreach}
				</table>
			
			<h4>STATUS_FRIEND</h4>
				<table border="1">
					<tr>
						<th>user_id</th><th>friend_id</th><th>status</th><th>date_bring</th><th>date_send_ticket</th><th>date_created</th><th>date_modified</th>
					</tr>
					{foreach from=$app.friend_list.2 item="row"}
					<tr>
						<td>{$row.user_id}</td><td>{$row.friend_id}</td><td align="right">{$row.status}</td><td>{$row.date_bring}</td><td>{$row.date_send_ticket}</td><td>{$row.date_created}</td><td>{$row.date_modified}</td>
					</tr>
					{/foreach}
				</table>

			<h3>到達エリア、エリア突入日時</h3>
			<h4>全デッキ情報</h4>
				デッキ？

			<h4>全所持アイテム</h4>
				<table border="1">
					<tr>
						<th>item_id</th><th>name</th><th>num</th>
					</tr>
					{foreach from=$app.item_list item="row"}
					<tr>
						<td align="right">{$row.item_id}</td><td>{$row.name}</td><td align="right">{$row.num}</td>
					</tr>
					{/foreach}
				</table>
			
			<h4>プレゼント情報</h4>
				<table border="1">
					<tr>
						<th>present_id</th>
						<th>user_id_from</th>
						<th>name</th>
						<th>user_id_to</th>
						<th>comment_id</th>
						<th>comment</th>
						<th>type</th>
						<th>item_id</th>
						<th>lv</th>
						<th>number</th>
						<th>status</th>
						<th>date_created</th>
						<th>date_modified</th>
					</tr>
					{foreach from=$app.present_list item="row"}
					<tr>
						<td>{$row.present_id}</td>
						<td>{$row.user_id_from}</td>
						<td>{$row.name}</td>
						<td>{$row.user_id_to}</td>
						<td>{$row.comment_id}</td>
						<td>{$row.comment}</td>
						<td>{$row.type}</td>
						<td>{$row.item_id}</td>
						<td>{$row.lv}</td>
						<td>{$row.number}</td>
						<td>{$row.status}</td>
						<td>{$row.date_created}</td>
						<td>{$row.date_modified}</td>
					</tr>
					{/foreach}
				</table>
			
			<h4>アイテム履歴（アイテム名/アイテムＩＤ/使用か取得か/個数/日時）</h4>
			<table border="1">
			{if $app.tracking_list}{foreach from=$app.tracking_list item="row" name="loop1"}
				{if $smarty.foreach.loop1.first}
					<tr>
					{foreach from=$row key="colname" item="value"}
						<th>{$colname}</th>
					{/foreach}
					</tr>
				{/if}
				
				<tr>
				{foreach from=$row item="value"}
					<td>{$value}</td>
				{/foreach}
				</tr>
			{/foreach}{/if}
			</table>
			（TODO:この付近にページングのリンクを置く予定）
			
			<h4>課金履歴（PaymentID/ステータス（購入確定か）/アイテム名/アイテムＩＤ/課金額/購入数/日時）</h4>
			※以下は魔法のメダル消費のみ。魔法のメダル入手はPaymentサーバ参照。
			<table border="1">
			{if $app.shop_list}{foreach from=$app.shop_list item="row" name="loop2"}
				{if $smarty.foreach.loop2.first}
					<tr>
					{foreach from=$row key="colname" item="value"}
						<th>{$colname}</th>
					{/foreach}
					</tr>
				{/if}
				
				<tr>
				{foreach from=$row item="value"}
					<td>{$value}</td>
				{/foreach}
				</tr>
			{/foreach}{/if}
			</table>
			（TODO:この付近にページングのリンクを置く予定）

		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
