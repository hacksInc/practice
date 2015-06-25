<?php
/**
 *  Pp_Error.php
 *
 *  @package   Pp
 *
 *  $Id$
 */

/*--- Application Error Definition ---*/
/*
 *  TODO: Write application error definition here.
 *        Error codes 255 and below are reserved 
 *        by Ethna, so use over 256 value for error code.
 *
 *  Example:
 *  define('E_LOGIN_INVALID', 256);
 */

// STATUS_DETAIL_CODE
define('SDC_HTTP_200',						2000);	// HTTPステータスコード200番台で特になにもない場合に返す
define('SDC_HTTP_400',						4000);	// HTTPステータスコード400番台でエラー詳細の指定がない場合に返す　400エラーの規定値
define('SDC_APPVER_NOT_LATEST',				4001);	// アプリバージョンが最新ではない
define('SDC_RSCVER_NOT_LATEST',				4002);	// リソースバージョンが最新ではない
define('SDC_HTTP_503_SERVICE_UNAVAILABLE',	4003);	// サーバメンテナンス中
													//  …HTTPステータスコード503番(Service Unavailable)に相当するが、
												 	//    SDC_APPVER_NOT_LATEST等と同様の4000番台にした方がクライアントアプリ側でわかりやすいようなので、
												 	//    5000番台ではなく4000番台にした。
define('SDC_USER_ACCESS_BAN',				4008);	// アクセス禁止ユーザエラー（BAN）
define('SDC_HTTP_401_UNAUTHORIZED',			4009);	// HTTPステータスコード401番(Unauthorized)の場合に返す

// 4010~4012はポータル側で使用する
define('SDC_USER_INPUT_ERROR',				4011);	// ユーザー作成時の入力エラー

define('SDC_USER_NAME_LENGTH_OVER',			4013);	// ユーザー名の文字数オーバー
define('SDC_USER_NAME_UNAVAILABLE',			4014);	// ユーザー名が使用不可（禁止文字などが含まれている）

define('SDC_HTTP_500',						5000);	// HTTPステータスコード500番台でエラー詳細の指定がない場合に返す　500エラーの規定値
define('SDC_DB_ERROR',						5001);	// DB処理エラー
define('SDC_USER_NONEXISTENCE',				5002);	// サイコパスIDが存在しない
define('SDC_USER_MIGRATE_ID_PW_ERROR',		5003);	// 引き継ぎID、引き継ぎパスワードが間違っている
define('SDC_USER_MIGRATE',					5004);	// 引き継ぎ処理エラー
define('SDC_AGE_VERIFY_VALUE_ERROR',		5005);	// 年齢認証値エラー
define('SDC_USER_PORTAL_ERROR',				5006);	// ポータル側ユーザー情報が存在しない

define('SDC_BOX_ID_ERROR',					5200);	// 取得しようとしたBOX-IDがプレゼントBOXに存在しない
define('SDC_BOX_MAX_ERROR',					5201);	// BOX内のアイテム数が上限に達しているためアイテムを取得できない。

define('SDC_SERIAL_INVALID_CODE',			5300);	// 無効なシリアルコード
define('SDC_SERIAL_USED_CODE',				5301);	// 使用済みのシリアルコード
define('SDC_SERIAL_CAMPAIGN_ERROR',			5302);	// 該当するキャンペーンが存在しない、または期限切れ
define('SDC_SERIAL_OVERLAP_ERROR',			5303);	// 同一ユーザが重複登録した

define('SDC_SHOP_ITEM_ERROR',				5400);	// 対象のアイテムが存在しない（販売期限切れなど）

// 5420~5499はクライアント側で使用するので、定義禁止！

define('SDC_THERAPY_ORDER_SHORTAGE',		5500);	// セラピー受診命令書不足
define('SDC_EX_STRESS_CARE_PARAM_ERROR',	5501);	// 臨時ストレスケアを実行するにはパラメータに問題がある
define('SDC_FIXED_STRESS_CARE_ERROR',		5502);	// 定時ストレスケア実行エラー
define('SDC_THERAPY_ORDER_PARAM_ERROR',		5503);	// セラピー受診を実行するにはパラメータに問題がある

define('SDC_PHOTO_GACHA_FILM_SHORTAGE',		5600);	// フォトフィルム不足によるガチャ実行不可
define('SDC_PHOTO_GACHA_CLOSE',				5601);	// フォトガチャ販売期限切れ
define('SDC_PHOTO_GACHA_ERROR',				5602);	// フォトガチャ関連エラー

define('SDC_MISSION_DRONE_SHORTAGE',		5700);	// 巡査ドローン不足
define('SDC_MISSION_ERROR',					5701);	// ミッション実行不可（期限切れなど）
define('SDC_MISSION_PLAYID_INVALID',		5702);	// 無効なプレイID
define('SDC_MISSION_CRIME_COEF_MAX',		5703);	// 犯罪係数が上限値なのでミッション実行不可
define('SDC_MISSION_SUPPORT_PARAM_ERROR',	5704);	// サポートキャラがミッション受注パラメータを満たしていない
define('SDC_MISSION_NO_SPARE_DOMINATOR',	5705);	// 予備ドミネーターがないため補充ができない
define('SDC_MISSION_AREA_STRESS_SHORTAGE',	5706);	// エリアストレスが下限値（ドローン使用時）

/*
define('SDC_HTTP_200',					2000);//HTTPステータスコード200番台で特になにもない場合に返す
define('SDC_HTTP_400',					4000);//HTTPステータスコード400番台でエラー詳細の指定がない場合に返す　400エラーの規定値
define('SDC_APPVER_NOT_LATEST',			4001);//アプリバージョンが最新ではない
define('SDC_RSCVER_NOT_LATEST',			4002);//リソースバージョンが最新ではない
define('SDC_HTTP_503_SERVICE_UNAVAILABLE', 4003);//サーバメンテナンス中
                                                 //  …HTTPステータスコード503番(Service Unavailable)に相当するが、
												 //    SDC_APPVER_NOT_LATEST等と同様の4000番台にした方がクライアントアプリ側でわかりやすいようなので、
												 //    5000番台ではなく4000番台にした。
define('SDC_USER_ACCESS_BAN',			4009);//アクセス禁止ユーザエラー（BAN）
define('SDC_HTTP_401_UNAUTHORIZED',		4010);//HTTPステータスコード401番(Unauthorized)の場合に返す
define('SDC_HTTP_500',					5000);//HTTPステータスコード500番台でエラー詳細の指定がない場合に返す　500エラーの規定値
define('SDC_DB_ERROR',					5001);//DB処理エラー
define('SDC_USER_NONEXISTENCE',			5002);//user_idが存在しない
define('SDC_TUTORIAL_VALUE_ERROR',		5003);//チュートリアル進捗値エラー
define('SDC_AGE_VERIFY_VALUE_ERROR',	5004);//年齢認証値エラー
define('SDC_RANKING_NO_ENTRY',			5005);//ランキングに入っていない
define('SDC_USER_MIGRATE',				5006);//データ移行エラー
define('SDC_UNIT_UNAVAILABLE',			5007);//ユニット使用不可エラー
define('SDC_FRIEND_ERROR',				5100);//フレンド処理エラー
define('SDC_FRIEND_MAX_ERROR',			5101);//フレンド数上限エラー
define('SDC_FRIEND_USER_NONEXISTENCE',	5102);//対象となるフレンドのuser_idが存在しない
define('SDC_FRIEND_EXISTENCE_ERROR',	5103);//既にフレンドになっている
define('SDC_FRIEND_REQUEST_ERROR',		5104);//既に申請状態
define('SDC_FRIEND_BLOCK_ERROR',		5105);//既にブロック状態
define('SDC_FRIEND_MAX_TARGET_ERROR',	5106);//相手のフレンド数上限エラー
define('SDC_FRIEND_ID_SAME_ERROR',		5107);//フレンドIDが同じエラー
define('SDC_QUEST_ERROR',				5200);//クエスト関連エラー
define('SDC_QUEST_HELPER_BRING_TIME',	5201);//助っ人を連れて行ける時間になっていない
define('SDC_QUEST_TMP_HELPER_DATA_SET',	5202);//テンポラリの助っ人データ保存に失敗
define('SDC_QUEST_START_ERROR',			5203);//クエストスタート時のエラー
define('SDC_QUEST_PLAYID_INVALID',		5204);//プレイIDが無効
define('SDC_QUEST_STAMINA_SHORTAGE',	5205);//スタミナ不足
define('SDC_QUEST_MONSTER_OVER',		5206);//所持モンスターオーバー
define('SDC_QUEST_BONUS_TYPE_ERROR',	5207);//ボーナスタイプエラー
define('SDC_QUEST_BONUS_CODE_ERROR',	5208);//ボーナスチェックコードエラー
define('SDC_QUEST_COST_OVER_ERROR',		5209);//クエストスタート時のコストオーバーエラー
define('SDC_QUEST_CLEAR_ERROR',			5210);//クエストクリア時のエラー
define('SDC_ITEM_ERROR',				5300);//アイテム関連エラー
define('SDC_ITEM_SHORTAGE',				5301);//アイテム不足
define('SDC_ITEM_USE_ERROR',			5302);//アイテム使用時エラー
define('SDC_COIN_SHORTAGE',				5303);//コイン不足
define('SDC_MEDAL_SHORTAGE',			5304);//魔法のメダル不足
define('SDC_PAYMENT_SVR_STATUS_ERROR',	5305);//課金サーバステータスエラー
define('SDC_SHOP_ERROR',				5306);//ショップ関連エラー
define('SDC_SHOP_CONSUME_ID_ERROR',		5307);//メダル消費量IDまたは消費量が不正
define('SDC_SHOP_MONSTER_BOX_MAX_ERROR',5308);//モンスターBOX上限エラー
define('SDC_SHOP_CONTINUE_TMP_NONEXISTENCE',5309);//コンティニュー時にクエストスタート一時データが存在しない
define('SDC_SERIAL_CAMPAIGN_ERROR',		5400);//該当するキャンペーンが存在しない、または期限切れ
define('SDC_SERIAL_OVERLAP_ERROR',		5401);//同一ユーザが重複登録した
define('SDC_SERIAL_USED_CODE',			5402);//使用済みコード
define('SDC_SERIAL_INVALID_CODE',		5403);//存在しないコード
define('SDC_RAID_ERROR',				5500);//レイド関連エラー
define('SDC_RAID_PARTYID_INVALID',		5501);//パーティIDが無効
define('SDC_RAID_SALLYNO_INVALID',		5502);//出撃NOが無効
define('SDC_RAID_POINT_SHORTAGE',		5503);//レイドポイント不足
define('SDC_RAID_UNQUALIFIED',		    5504);//（パーティマスターの）ダンジョンへの出撃資格がない

define('SDC_INAPI_ARGUMENT_ERROR',		5600);//Inapi引数エラー
define('SDC_INAPI_PARTY_NOT_READY',		5601);//パーティステータスが準備中でないのにクエスト開始を実行
define('SDC_INAPI_DUNGEON_UNQUALIFIED',	5602);//パーティマスターに出撃対象のダンジョンへの出撃資格がない
define('SDC_INAPI_DB_ERROR',			5603);//InapiでのDB処理エラー
define('SDC_INAPI_REWARD_ERROR',		5604);//報酬配布エラー
define('SDC_INAPI_PARTY_NOT_SALLY',		5605);//パーティステータスが出撃中でないのに出撃を実行
define('SDC_INAPI_FINISHED_SALLY_NO',	5606);//既に終了しているクエストの出撃NO
define('SDC_INAPI_POINT_SHORTAGE',		5607);//レイドポイント不足
define('SDC_INAPI_PARTY_BRAKEUP',		5608);//既に解散したパーティにエントリーしようとした
define('SDC_INAPI_PARTY_MEMBER_MAX',	5609);//メンバー数が上限に達しているパーティにエントリーしようとした
define('SDC_INAPI_FORCE_LEAVE_NO_MASTER',5610);//パーティマスター以外のメンバーによる強制退室の操作
define('SDC_INAPI_FORCE_LEAVE_REENTRY',	5611);//強制退室させられた部屋に再入室しようとした
define('SDC_INAPI_LEAVE_REENTRY',		5612);//自主退室した部屋に再入室しようとした
define('SDC_INAPI_FORCE_LEAVE_ACTIVE',	5613);//5%以上ダメージを与えたメンバーを強制退室させようとした
define('SDC_INAPI_QUEST_TIMEOUT',		5614);//時間切れのクエストに出発しようとした
define('SDC_INAPI_PASSWD_NOT_MATCH',	5615);//入室パスワードが一致していない
define('SDC_INAPI_DUNGEON_OVERTIME',	5616);//選択されているダンジョンは開催期間外
define('SDC_INAPI_FORCE_LEAVE_MASTER',	5617);//パーティマスターを強制退室させようとした

define('SDC_BADGE_ERROR',				5700);//バッジ関連エラー
define('SDC_BADGE_MAX_ERROR',			5701);//バッジ取得（＆生成）時にバッジ所持数が上限オーバーになる
define('SDC_BADGE_MATERIAL_SHORTAGE',	5702);//バッジ生成時に素材が足りない
define('SDC_BADGE_OWN_ERROR',			5703);//バッジ装着時に指定バッジを所持していない
define('SDC_BADGE_EFFECT_OVERLAP',		5704);//バッジ装着時に効果が重複
define('SDC_BADGE_BASE_ERROR',			5705);//バッジ装着時に上書き元に指定された「元バッジID」が存在しない
define('SDC_BADGE_DELETE_ERROR',		5706);//バッジ削除時に指定された個数が大きい（所持分が足りない）指定された個数が0以下（指定エラー）
define('SDC_BADGE_MATERIAL_MAX_ERROR',	5707);//バッジ素材取得時にバッジ素材所持数が上限オーバーになる
define('SDC_BADGE_EXPAND_ERROR',		5708);//バッジ枠拡張エラー（上限オーバー）
define('SDC_BADGE_OUT_OF_PERIOD',		5709);//バッジ生成不可（生成期限外）
define('SDC_BADGE_NO_MATERIAL_ERROR',	5710);//バッジ生成不可（素材指定なし）
*/


?>