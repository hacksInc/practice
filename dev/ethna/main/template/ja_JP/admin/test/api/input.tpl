<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	{include file="admin/common/head.tpl" title="API検証"}
	<body>
		{include file="admin/common/navbar.tpl"}
		<div class="container-fluid">
		{include file="admin/common/breadcrumb.tpl"}
		<h1>API検証</h1>
		
		<form method="POST" action="exec">
			環境<br />
			{strip}
			<label style="display:inline-block;">
				{if Util::getEnv() == "dev"}
					<input type="radio" name="env" value="dev" checked />
					開発
				{else}
					<input type="radio" name="env" value="dev" disabled />
					<span class="muted">開発</span>
				{/if}
			</label>&nbsp;&nbsp;
			<label style="display:inline-block;">
				{if Util::getEnv() == "stg"}
					<input type="radio" name="env" value="stg" checked />
					ステージング
				{else}
					<input type="radio" name="env" value="stg" disabled />
					<span class="muted">ステージング</span>
				{/if}
			</label>&nbsp;&nbsp;
			<label style="display:inline-block;">
				<input type="radio" name="env" value="pro" />商用
			</label>&nbsp;&nbsp;
{*
			<input type="radio" name="env" value="st" />負荷テスト&nbsp;&nbsp;
			<input type="radio" name="env" value="ost" />負荷テスト(OST)&nbsp;&nbsp;<br />
*}
			<label style="display:inline-block;">
				<input type="radio" name="env" value="local" />ローカル
			</label>&nbsp;&nbsp;<br />
			{/strip}
{*
			&nbsp;※商用は近日、このフォームから利用不可にする予定。<br />
*}
			<br />
			パス<br />
			api<input type="text" name="path" />例:[/user/get]<br />
			<br />
			バージョン<br />
			X-Jugmon-Appver<br />
			<input type="text" name="appver" value="{$app.appver}" /><br />
			X-Jugmon-Rscver<br />
			<input type="text" name="rscver" value="{$app.rscver}" /><br />
{*
			<br />
			ユニット<br />
			X-Jugmon-Unit<br />
			<input type="text" name="unit" value="*}{*{$app.unit}*}{*" /><br />
			※/user/createと/user/migrateでは指定してはならないので注意。<br />
*}
			<br />
			BASIC認証<br />
			サイコパス内ユーザID<br />
			<input type="text" name="uid" /><br />
			ユニークインストールパスワード<br />
			<input type="text" name="uipw" /><br />
{*
			※<a href="/admin_dev_log/test_account.log">開発環境でのアカウント関連のログはこちら</a><br />
			<br />
*}
{*
			共有キー<br />
			<input type="radio" name="pw" value="appw" />アプリパスワード<br />
			<input type="radio" name="pw" value="uipw" />ユニークインストールパスワード<br />
*}
			<input type="hidden" name="pw" value="appw" />
			<br />
			引数(JSON)<br />
			<textarea name="json" cols="80" rows="20" style="width: 50%;"></textarea><br />
			<br />
			<input type="submit" value="送信" />
		</form>
		
		<h2>テンプレ</h2>
<pre>{literal}		
/quest/list
{
"quest_type":1
}

/quest/start
{
"helper_id": ,
"helper_monster_id": ,
"area_id": ,
"quest_id": 
}

/user/dmpw/set
{
"account":"",
"dmpw":"",
"new_dmpw":""
}

/monster/team/set
{
  "user_team":[
    {"team_id":0,"pos1":-1,"leader":0,"pos2":-1,"pos3":-1,"pos4":-1,"pos5":-2},{"team_id":1,"pos1":-1,"leader":0,"pos2":-1,"pos3":-1,"pos4":-1,"pos5":-2}
  ],
  "active_team_id":0
}

/user/name/check
{
  "name":""
}

/resource/check
{
}

/resource/assetbundle/p
{
"dir":"monster",
"file_name":"monster_atlas_00001",
"device_name":"iPhone"
}

/shop/point/purchase
{
  "game_transaction_id":"(DB参照)",
  "receipt_product_id":"CI0022T_MM_1",
  "google_transaction_or_apple_receipt":"test_apple_receipt"
}

/shop/point/purchase
{
  "game_transaction_id":"(DB参照)",
  "receipt_product_id":"ci0022t_mm_1",
  "google_transaction_or_apple_receipt":"test_google_transaction",
  "google_signature":"test_google_signature"
}

/shop/medal/use
{
  "consume_id":3,
  "price":50,
  "game_transaction_id":"(DB参照)"
}

/user/create
{
  "ua":1,
  "device_info":"{\"deviceModel\":\"Macmini5,3\",\"deviceName\":\"CD-1108785\",\"deviceType\":\"Desktop\",\"deviceUniqueIdentifier\":\"E0DCA541-6C27-54E3-8F61-396B2167D794\",\"operatingSystem\":\"Mac OS X 10.8.5\",\"systemMemorySize\":\"4096\"}"
}

/user/migrate
{
  "account":"abcdefghijkl",
  "dmpw":"1234567890",
  "ua":1,
  "device_info":"{\"deviceModel\":\"Macmini5,3\",\"deviceName\":\"CD-1108785\",\"deviceType\":\"Desktop\",\"deviceUniqueIdentifier\":\"E0DCA541-6C27-54E3-8F61-396B2167D794\",\"operatingSystem\":\"Mac OS X 10.8.5\",\"systemMemorySize\":\"4096\"}"
}

/raid/search
{
  "play_style":0,
  "dungeon_id":0,
  "dungeon_rank":0,
  "party_status":0,
  "auto_entry":1
}

/raid/point/continue
{
  "continue_id":"abc12345678901234567890123456789",
  "cs_data":{
    "area_id":1,
    "quest_id":2,
    "play_id":3,
    "continue_num":4,
    "total_game_num":5,
    "overkill":[6,7],
    "bonus_overkill":[8,9],
    "drop_gold":10,
    "peka":11,
    "rb":12,
    "bb":13,
    "evo_medal":[14,15]
  }
}

/badge/expand
{
  "user_monster_id":拡張するユーザモンスターのID
}

{/literal}</pre>

		<hr>
		{include file="admin/common/footer.tpl"}
		</div><!--/.fluid-container-->
		{include file="admin/common/script.tpl"}
	</body>
</html>
