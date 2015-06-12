<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	{include file="admin/common/head.tpl" title="API検証"}
    <body>
		{include file="admin/common/navbar.tpl"}
		<div class="container-fluid">
        <h1>テストデータ生成</h1>
		{if $app.env_name}[{$app.env_name}]{/if}
        
		<h2>個別ユーザ</h2>
        <form method="POST" action="exec">
{*
            <input type="radio" name="user_type" value="create" checked />新規ユーザ<br />
            <input type="radio" name="user_type" value="update" />既存ユーザ　サイコパス内ユーザID<input type="text" name="uid" /><br />
*}
            <input type="hidden" name="user_type" value="update" />サイコパス内ユーザID<input type="text" name="uid" /><br />
			<br />
			<input type="checkbox" name="add[]" value="monster" />モンスター追加（ランダム）<br />
			　　…所持モンスターを10体、新規追加します。<br />
			<br />
			モンスター追加（ID指定）<br />
				monster_id:<input type="text" name="monster_id"><br />
				lv:<input type="text" name="monster_lv"><br />
			　　…モンスターを1体、新規追加します。<br />
			  　&nbsp;&nbsp;&nbsp;&nbsp;lvは省略可です。lvを指定する場合、マスターデータ（m_monsterテーブル）で設定されたmax_lv以内にして下さい。<br />
			<input type="checkbox" name="add[]" value="friend"  />フレンド追加<br />
			　　…ランダムにフレンド追加します。<br />
			<br />
			<input type="checkbox" name="add[]" value="team" />チーム編成<br />
			　　…ランダムにチーム編成します。<br />
			<br />
{*
			<input type="checkbox" name="add[]" value="gold" />ゴールド追加<br />
			　　…ゴールドを10000追加します。<br />
*}
			ゴールド追加<br />
			<input type="text" name="gold"><br />
			　　…ゴールドを追加します。<br />
			<br />
			ガチャポイント追加<br />
			<input type="text" name="gacha_point"><br />
			　　…ガチャポイントを追加します。<br />
			<br />
			経験値更新<br />
			<input type="text" name="exp"><br />
			　　…経験値を更新します。差分ではなく更新後の値を指定して下さい。<br />
			<br />
			<input type="checkbox" name="item_add[]" value="1001" />「進化（レア）メダル1」追加<br />
			　　…「進化（レア）メダル1」(item_id:1001)を100追加します。<br />
			<br />
			<input type="checkbox" name="item_add[]" value="1002" />「進化（レア）メダル2」追加<br />
			　　…「進化（レア）メダル2」(item_id:1002)を100追加します。<br />
			<br />
			<input type="checkbox" name="item_add[]" value="1003" />「進化（レア）メダル3」追加<br />
			　　…「進化（レア）メダル3」(item_id:1003)を100追加します。<br />
			<br />
			<input type="checkbox" name="item_add[]" value="1004" />「進化（レア）メダル4」追加<br />
			　　…「進化（レア）メダル4」(item_id:1004)を100追加します。<br />
			<br />
			<input type="checkbox" name="item_add[]" value="1005" />「進化（レア）メダル5」追加<br />
			　　…「進化（レア）メダル5」(item_id:1005)を100追加します。<br />
			<br />
			ポイント管理サーバ<br />
			ゲームトランザクションID：<input type="text" name="game_transaction_id"><br />
			アイテム数：<input type="text" name="item_count"><br />
            <input type="radio" name="point_type" value="gamebonus" checked />ゲーム内サービス付与
            <input type="radio" name="point_type" value="consume" />消費<br />
			<br />
           <input type="submit" value="実行" />
        </form>

{*
		<hr />
		<h2>既存ユーザのみ可能な編集項目</h2>
        <form method="POST" action="exp/update">
            サイコパス内ユーザID<input type="text" name="uid" /><br />
			経験値<input type="text" name="exp" /><br />
            <input type="submit" value="更新" /><br />
			　…ランク等の値も経験値に応じて自動更新されます。
        </form>
*}

		<hr />
		<h2>一括変更</h2>
		<a href="logindate/update">ログイン日時更新</a><br />
		　…ログイン日時が3日以上前の全ユーザについて、ログイン日時を現在に変更します。<br />
		　　※ログイン日時がNULLのユーザは変更しません。<br />
		
{*		  
		<hr />
		<h2>ケイブPaymentサーバ</h2>
		<a href="paymentserver/create">アカウント新規作成</a><br />
		　…ケイブPaymentサーバのアカウントを新規作成しますが、サイコパスのユーザは生成しません。<br />
*}
		</div><!--/.fluid-container-->
    </body>
</html>
