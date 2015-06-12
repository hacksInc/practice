<?php
/**
 *  Pp_RaidPartyManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_RaidManager.php';

/**
 *  Pp_RaidPartyManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_RaidPartyManager extends Pp_RaidManager
{
	// エントリータイプ（どうやって来たの？）
	const ENTRY_TYPE_AUTO   = 1;		// 自動入室
	const ENTRY_TYPE_SEARCH = 2;		// 検索機能経由
	const ENTRY_TYPE_FRIEND = 3;		// フレンド戦歴

	// パーティステータス
	const PARTY_STATUS_NONE    = 0;		// 指定なし（検索条件用の定義）
	const PARTY_STATUS_READY   = 1;		// 準備中
	const PARTY_STATUS_QUEST   = 2;		// 出撃中
	const PARTY_STATUS_BREAKUP = 3;		// 解散

	// プレイスタイル
	const PLAY_STYLE_NONE    = 0;		// なし
	const PLAY_STYLE_WELCOME = 1;		// 初心者熱烈歓迎！
	const PLAY_STYLE_MYPACE  = 2;		// マイペースで！（正確には'my own pace'が正しいが）
	const PLAY_STYLE_TOP     = 3;		// TOPに向かってひた走れ！

	// パーティメンバーステータス
	const MEMBER_STATUS_READY    = 1;	// 準備中
	const MEMBER_STATUS_STAND_BY = 2;	// 出撃準備完了
	const MEMBER_STATUS_RECOVER  = 3;	// 回復中
	const MEMBER_STATUS_BATTLE   = 4;	// 戦闘中
	const MEMBER_STATUS_MAP      = 5;	// 探索中
	const MEMBER_STATUS_BREAK    = 6;	// 自主退室
	const MEMBER_STATUS_FORCE    = 7;	// 強制退室
	const MEMBER_STATUS_MIGRATE  = 8;	// 端末引継中

	// 出撃メンバーステータス
	const SALLY_STATUS_READY    = 1;	// 準備中
	const SALLY_STATUS_STAND_BY = 2;	// 準備完了
	const SALLY_STATUS_RECOVER  = 3;	// 回復中
	const SALLY_STATUS_BATTLE   = 4;	// 戦闘中
	const SALLY_STATUS_MAP      = 5;	// 探索中
	const SALLY_STATUS_RETIRE   = 6;	// 自主退室
	const SALLY_STATUS_FORCE    = 7;	// 強制退室
	const SALLY_STATUS_MIGRATE  = 8;	// 端末引継中
	const SALLY_STATUS_WIN      = 20;	// 勝利
	const SALLY_STATUS_LOSE     = 21;	// 敗北（時間切れ）

	// 退室理由
	const LEAVE_TYPE_SELF  = 1;			// 自主退室
	const LEAVE_TYPE_FORCE = 2;			// 強制退室
	const LEAVE_TYPE_AUTO  = 3;			// 時間経過による自動退室
	
	// ユーザタイプ
	const USER_TYPE_MASTER = 1;	// マスター
	const USER_TYPE_MEMBER = 2;	// メンバー

	/**
	 * パーティ情報の取得
	 * 
	 * @param int $party_id パーティID
	 * @param boolean $for_update 読み取りロックをするか（true:する, false:しない）
	 * @param boolean $from_master マスターDBから取得するか（true:マスターから, false:スレーブから）
	 *
	 * @return パーティ情報
	 */
	function getParty( $party_id, $for_update = false, $from_master = false )
	{
		$param = array( $party_id );
		$sql = "SELECT * FROM t_raid_party WHERE party_id = ? ";
		if( $for_update === true )
		{
			$sql .= "FOR UPDATE";
		}
		if( $from_master === true )
		{
			return $this->db_unit1->GetRow( $sql, $param );
		}
		else
		{
			return $this->db_unit1_r->GetRow( $sql, $param );
		}
	}

	/**
	 * パーティ情報の更新
	 * 
	 * @param int $party_id パーティID
	 * @param array $columns 更新するカラム名とパラメータの連想配列
	 *
	 */
	function updateParty( $party_id, $columns )
	{
		$param = array();
		$set_string = array();
		foreach( $columns as $column => $value )
		{
			$set_string[] = $column.' = ?';
			$param[] = $value;
		}
		$param[] = $party_id;
		$sql = "UPDATE t_raid_party SET ".implode( ',', $set_string )." WHERE party_id = ?";
		return $this->db_unit1->execute( $sql, $param );
	}

	/**
	 * パーティ検索コードの取得
	 * 
	 * @param string $login_passwd 入室パスワード（一応0or1でも大丈夫なようにはなっているが）
	 * @param int $difficulty 難易度
	 * @param int $force_elimination 強制退室設定（0:OFF, 1:ON）
	 * @param int $play_style プレイスタイル（1:初心者熱烈歓迎, 2:マイペースで, 3:トップを目指す！）
	 *
	 * @return 検索コード
	 */
//	function getSearchCode( $login_passwd, $difficulty, $play_style, $force_elimination )
//	{
//		$auto_login = ( empty( $login_passwd ) === true ) ? 0 : 1;
//		$param = array( $auto_login, $difficulty, $play_style, $force_elimination );
//		$sql = "SELECT code FROM m_raid_party_search_correspondence_table "
//			 . "WHERE auto_login = ? AND difficulty = ? AND play_style = ? AND force_elimination = ?";
//		return $this->db_unit1_r->GetOne( $sql, $param );
//	}
	
	/**
	 * パーティ情報の作成
	 * 
	 * @param int $user_id ユーザーID
	 * @param int $dungeon_id ダンジョンID
	 * @param int $difficulty 難易度
	 * @param int $dungeon_lv ダンジョンLV
	 * @param int $force_elimination 強制退室設定（0:OFF, 1:ON）
	 * @param int $play_style プレイスタイル（1:初心者熱烈歓迎, 2:マイペースで, 3:トップを目指す！）
	 * @param string $login_passwd 入室パスワード（空文字の場合は自動入室ON）
	 * @param int $message メッセージコメント
	 *
	 * @return パーティID
	 */
	function createParty($user_id, $dungeon_id, $difficulty, $dungeon_lv, $force_elimination, $play_style, $login_passwd, $message)
	{
		// Configパラメータを取得
		$config = $this->backend->config->get( 'raid_config' );

		// パーティ検索コードを取得
//		$search_code = $this->getSearchCode( $login_passwd, $difficulty, $play_style, $force_elimination );
		
		$transaction = ( $this->db_unit1->db->transCnt == 0 ) ? true : false; // トランザクション開始するか？
		if( $transaction )
		{	// トランザクション開始
			$this->db_unit1->begin();
		}

		// パーティ情報を作成
		$param = array(
			$user_id, $user_id, $dungeon_id, $difficulty, $dungeon_lv, 1, $config['party_member_limit'], 1,
			self::PARTY_STATUS_READY, $force_elimination, $play_style, $login_passwd, $message
		);
		$sql = "INSERT INTO t_raid_party( create_user_id, master_user_id, dungeon_id, difficulty, dungeon_lv, "
			 . "  member_num, member_limit, member_max, status, force_elimination, play_style, "
			 . "  entry_passwd, message_id, date_created ) "
			 . "VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
		if( !$this->db_unit1->execute( $sql, $param ))
		{	// 書き込みエラー
			if( $transaction === true )
			{	// ロールバックする
				$this->db_unit1->rollback();
			}
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}
		$party_id = $this->db_unit1->db->Insert_ID();		// 作成されたレコードのパーティIDを取得

		// パーティ作成ユーザーをメンバーに追加
		$hash = $this->_addMember( $party_id, $user_id );
		if( is_null( $hash ) === true )
		{	// パーティメンバーの追加エラー
			if( $transaction === true )
			{	// ロールバックする
				$this->db_unit1->rollback();
			}
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}

		if( $transaction === true )
		{	// コミットする
			$this->db_unit1->commit();
		}

		return $party_id;
	}

	/**
	 * パーティメンバーの追加
	 * 
	 * @param int $party_id パーティID
	 * @param int $user_id ユーザーID
	 * 
	 * @return
	 */
	function addPartyMember( $party_id, $user_id )
	{
		// 既にパーティにいるか？
		$member = $this->getPartyMember( $party_id, $user_id );
		if(( !is_array( $member ))||( Ethna::isError( $member )))
		{	// 取得エラー
			return false;
		}
		if( empty( $member ) === false )
		{	// 既にパーティにいる（再参加）
			$columns = array(
				'status' => self::MEMBER_STATUS_READY,			// ステータスを準備中に戻す
				'disconn' => 0,									// 切断フラグをリセット
				'disconn_status' => 0,							// 切断時ステータスもリセット
				'date_created' => date( 'Y-m-d H:i:s', time())	// 参加日時を現在の時刻に
			);
			$ret = $this->updatePartyMember( $party_id, $user_id, $columns );
			if( !ret )
			{
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}
		else
		{
			// ユーザーをメンバーに追加
			$hash = $this->_addMember( $party_id, $user_id );
			if( is_null( $hash ) === true )
			{	// パーティメンバーの追加エラー
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}

		$status = array(	// 退室した人以外のステータス全て
			self::MEMBER_STATUS_READY,		// 準備中の人
			self::MEMBER_STATUS_STAND_BY,	// 出撃準備完了の人
			self::MEMBER_STATUS_RECOVER,	// 回復中の人
			self::MEMBER_STATUS_BATTLE,		// 戦闘中の人
			self::MEMBER_STATUS_MAP			// 探索中の人
		);
		$party_members = $this->getPartyMembers( $party_id, $status, true, true );

		// パーティの人数情報を変更
		$n = count( $party_members );
		if( $this->_setMemberNum( $party_id, $n ) === false )
		{
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
		}

		return true;
	}

	/**
	 * パーティメンバーの取得
	 * 
	 * @param int $party_id パーティID
	 * @param int $user_id ユーザーID
	 * @param boolean $from_master マスターDBから取得するか（true:マスターから, false:スレーブから）
	 * 
	 * @return パーティメンバー情報
	 */
	function getPartyMember( $party_id, $user_id, $from_master = false )
	{
		$param = array( $party_id, $user_id );
		$sql = "SELECT * FROM t_raid_party_member WHERE party_id = ? AND user_id = ?";
		if( $from_master === false )
		{
			return $this->db_unit1_r->GetRow( $sql, $param );
		}
		else
		{
			return $this->db_unit1->GetRow( $sql, $param );
		}
	}

	/**
	 * 同一パーティIDの全メンバーの取得
	 * 
	 * @param int $party_id パーティID
	 * @param array $status 取得対象のステータスの配列（空配列orNULLの場合はステータス指定なし）
	 * @param boolean $for_update 読み取りロックをするか（true:する, false:しない）
	 * @param boolean $from_master マスターDBから取得するか（true:マスターから, false:スレーブから）
	 * 
	 * @return パーティメンバー情報の配列
	 */
	function getPartyMembers( $party_id, $status = null, $for_update = false, $from_master = false )
	{
		$param = array( $party_id );
		$sql = "SELECT * FROM t_raid_party_member WHERE party_id = ? ";
		if(( is_array( $status ) === true )&&( empty( $status ) === false ))
		{
			$where_in = array();
			foreach( $status as $v )
			{
				$param[] = $v;
				$where_in[] = '?';
			}
			$sql .= "AND status IN (".implode( ',', $where_in ).") ORDER BY date_created ";
		}
		if( $for_update === true )
		{
			$sql .= "FOR UPDATE";
		}
		if( $from_master === false )
		{
			return $this->db_unit1_r->GetAll( $sql, $param );
		}
		else
		{
			return $this->db_unit1->GetAll( $sql, $param );
		}
	}

	/**
	 * ハッシュ値によるパーティメンバーの取得
	 * 
	 * @param string $hash ユーザー識別コード
	 * 
	 * @return パーティメンバー情報
	 */
	function getPartyMemberByHash( $hash )
	{
		$param = array( trim( $hash ));
		$sql = "SELECT * FROM t_raid_party_member WHERE hash = ?";
		return $this->db_unit1_r->GetRow( $sql, $param );
	}

	/**
	 * パーティメンバーのパラメータを更新
	 * 
	 * @param int $party_id パーティID
	 * @param int $user_id 更新ユーザーID
	 * @param array $columns 更新用パラメータの連想配列
	 * 
	 * @return
	 */
	function updatePartyMember( $party_id, $user_id, $columns )
	{
		$param = array();
		$set_string = array();
		foreach( $columns as $column => $value )
		{
			$set_string[] = $column.' = ?';
			$param[] = $value;
		}
		array_push( $param, $party_id, $user_id );
		$sql = "UPDATE t_raid_party_member SET ".implode( ',', $set_string )
			 . " WHERE party_id = ? AND user_id = ?";
		return $this->db_unit1->execute( $sql, $param );
	}

	/**
	 * パーティメンバーの最新の１件を取得
	 * 
	 * @param int $user_id ユーザーID
	 * 
	 * @return パーティメンバー情報
	 */
	function getPartyMemberNewest( $user_id )
	{
		$param = array( $user_id );
	//	$sql = "SELECT * FROM t_raid_party_member WHERE user_id = ? AND disconn = 0 ORDER BY date_created DESC LIMIT 1";
		$sql = "SELECT * FROM t_raid_party_member WHERE user_id = ? ORDER BY date_created DESC LIMIT 1";
		return $this->db_unit1_r->GetRow( $sql, $param );
	}

	/**
	 * 指定のユーザーのパーティメンバー情報を切断状態にする
	 * 
	 * @param int $user_id ユーザーID
	 * 
	 * @return
	 */
	function setDisconnPartyMember( $user_id )
	{
		$param = array( $user_id );
		$sql = "UPDATE t_raid_party_member SET disconn = 1 WHERE user_id = ? AND disconn = 0";
		return $this->db_unit1->execute( $sql, $param );
	}

	/**
	 * 指定のユーザーのパーティメンバーステータスを端末引継状態にする
	 * 
	 * @param int $user_id ユーザーID
	 * 
	 * @return
	 */
	function setMigratePartyMember( $user_id )
	{
		$param = array( self::MEMBER_STATUS_MIGRATE, $user_id, self::MEMBER_STATUS_READY , self::MEMBER_STATUS_MAP );
		$sql = "UPDATE t_raid_party_member SET status = ?, disconn = 1 WHERE user_id = ? AND status >= ? AND status <= ?";
		return $this->db_unit1->execute( $sql, $param );
	}

	/**
	 * 指定のユーザーの端末引継状態のレコードを取得する
	 * 
	 * @param int $user_id ユーザーID
	 * 
	 * @return パーティメンバー情報の配列
	 */
	function getMigratePartyMember( $user_id )
	{
		$param = array( $user_id, self::MEMBER_STATUS_MIGRATE );//←下のSQLと数が合ってなくなくなくない？
		$sql = "SELECT * FROM t_raid_party_member WHERE user_id = ? AND status = ?";
		return $this->db_unit1_r->GetAll( $sql, $param );
	}

	/**
	 * 出撃メンバーの追加
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃番号
	 * @param array $user_ids 出撃するユーザーIDの配列
	 * 
	 * @return
	 */
	function addSallyMember( $party_id, $sally_no, $user_ids )
	{
		$transaction = ( $this->db_unit1->db->transCnt == 0 ) ? true : false; // トランザクション開始するか？
		if( $transaction )
		{	// トランザクション開始
			$this->db_unit1->begin();
		}

		foreach( $user_ids as $user_id )
		{
			$param = array( $party_id, $sally_no, $user_id, self::SALLY_STATUS_BATTLE );
			$sql = "INSERT INTO t_raid_sally_member( party_id, sally_no, user_id, status, date_created )"
				 . "VALUES( ?, ?, ?, ?, NOW())";
			if( !$this->db_unit1->execute( $sql, $param ))
			{
				if( $transaction === true )
				{	// ロールバックする
					$this->db_unit1->rollback();
				}
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db_unit1->db->ErrorNo(), $this->db_unit1->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}

		if( $transaction === true )
		{	// コミットする
			$this->db_unit1->commit();
		}

		return true;
	}

	/**
	 * 出撃メンバーのパラメータを更新
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃番号
	 * @param array $user_id 更新するユーザーID
	 * @param array $columns 更新用パラメータの連想配列
	 * 
	 * @return パーティメンバー情報
	 */
	function updateSallyMember( $party_id, $sally_no, $user_id, $columns )
	{
		$param = array();
		$set_string = array();
		foreach( $columns as $column => $value )
		{
			$set_string[] = $column.' = ?';
			$param[] = $value;
		}
		array_push( $param, $party_id, $sally_no, $user_id );
		$sql = "UPDATE t_raid_sally_member SET ".implode( ',', $set_string )." "
			 . "WHERE party_id = ? AND sally_no = ? AND user_id = ?";
		return $this->db_unit1->execute( $sql, $param );
	}

	/**
	 * 指定の出撃メンバーのパラメータを一括更新
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃番号
	 * @param array $user_ids 更新するユーザーIDの配列
	 * @param array $columns 更新用パラメータの連想配列
	 * 
	 * @return
	 */
	function updateSallyMembers( $party_id, $sally_no, $user_ids, $columns )
	{
		$param = array();
		$set_string = array();

		foreach( $columns as $column => $value )
		{
			$set_string[] = $column.' = ?';
			$param[] = $value;
		}
		array_push( $param, $party_id, $sally_no );
		$where_user_id_in = array();
		foreach( $user_ids as $u )
		{
			$param[] = $u;
			$where_user_id_in[] = '?';
		}
		$sql = "UPDATE t_raid_sally_member SET ".implode( ',', $set_string )." "
			 . "WHERE party_id = ? AND sally_no = ? AND user_id IN (".implode( ',', $where_user_id_in ).")";
		return $this->db_unit1->execute( $sql, $param );
	}

	/**
	 * 出撃メンバーの取得
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃NO
	 * @param int $user_id ユーザーID
	 * 
	 * @return 出撃メンバー情報
	 */
	function getSallyMember( $party_id, $sally_no, $user_id )
	{
		$param = array( $party_id, $sally_no, $user_id );
		$sql = "SELECT * FROM t_raid_sally_member WHERE party_id = ? AND sally_no = ? AND user_id = ?";
		return $this->db_unit1_r->GetRow( $sql, $param );
	}

	/**
	 * 同一パーティIDの全出撃メンバーの取得
	 * 
	 * @param int $party_id パーティID
	 * @param int $sally_no 出撃NO
	 * @param array $status 取得対象のステータスの配列（空配列orNULLの場合はステータス指定なし）
	 * 
	 * @return 出撃メンバー情報の配列
	 */
	function getSallyMembers( $party_id, $sally_no, $status = null )
	{
		$param = array( $party_id, $sally_no );
		$sql = "SELECT * FROM t_raid_sally_member WHERE party_id = ? AND sally_no = ? ";
		if( empty( $status ) === false )
		{	// 配列
			$where_in = array();
			foreach( $status as $v )
			{
				$param[] = $v;
				$where_in[] = '?';
			}
			$sql .= "AND status IN (".implode( ',', $where_in ).") ";
		}
		return $this->db_unit1_r->GetAll( $sql, $param );
	}

	/**
	 * 最新の出撃NOから出撃メンバーの取得
	 * 
	 * @param int $party_id パーティID
	 * @param int $user_id ユーザーID
	 * 
	 * @return 出撃メンバー情報
	 */
	function getSallyMemberLatestSallyno( $party_id, $user_id )
	{
		$param = array( $party_id, $user_id );
		$sql = "SELECT * FROM t_raid_sally_member WHERE party_id = ? AND user_id = ? ORDER BY sally_no DESC LIMIT 1";
		return $this->db_unit1_r->GetRow( $sql, $param );
	}

	/**
	 * 出撃メンバーの最新の１件を取得
	 * 
	 * @param int $user_id ユーザーID
	 * 
	 * @return 出撃メンバー情報
	 */
	function getSallyMemberNewest( $user_id )
	{
		$param = array( $user_id );
		$sql = "SELECT * FROM t_raid_sally_member WHERE user_id = ? ORDER BY date_created DESC LIMIT 1";
		return $this->db_unit1_r->GetRow( $sql, $param );
	}
	
	/**
	 * パーティ情報をAPI戻り値用のフォーマットへ変換する
	 * 
	 * 戻り値は以下の書式の連想配列
	 * <code>
	 * array(
	 * 　"party_id" => パーティーID,
	 * 　"party_num" => パーティー人数,
	 * 　"party_max_num" => 最大パーティー人数,
	 * 　"play_style" => プレイスタイル,
	 * 　"auto_join" => 自動入室ON=1、OFF=0（パスワードが空文字列の時は1）,
	 * 　"force_reject" => 強制退室,
	 * 　"pass" => パスワードフラグ（1:有, 0:無）,
	 * 　"status" => パーティーのステータス,
	 * )
	 * </code>
	 * @param array $party パーティ（t_raid_partyの1行に相当する連想配列）
	 * @return array パーティ情報
	 */
	function convertPartyInfoForApiResponse($party)
	{
		$party_info = array(
			'party_id'      => $party['party_id'],
			'party_num'     => $party['member_num'],
			'party_max_num' => $party['member_limit'],
			'play_style'    => $party['play_style'],
			'auto_join'     => (strlen($party['entry_passwd']) == 0 ? 1 : 0),//entry_passwdが空文字列だったら自動入室
			'force_reject'  => $party['force_elimination'],
//			'pass'          => $party['entry_passwd'],
			'pass'          => (strlen($party['entry_passwd']) > 0) ? 1 : 0,
			'status'        => $party['status'],
		);

		return $party_info;
	}

	/**
	 * パーティのユーザー情報をAPI戻り値用のフォーマットへ変換する
	 * 
	 * 戻り値は以下の書式の連想配列
	 * <code>
	 * array (
	 * 　"user_id" => ユーザID,
	 * 　"user_name" => ユーザ名,
	 * 　"user_rank" => ユーザランク,
	 * 　"leader_mons_id" => リーダーモンスターのID,
	 * 　"leader_mons_lv" => リーダーモンスターのレベル,
	 * 　"leader_mons_exp" => リーダーモンスターの経験値,
	 * 　"leader_mons_skill_lv" => リーダーモンスターのスキルレベル,
	 * 　"user_type" => ユーザタイプ(1:マスター,2:メンバー),　←合ってる？
	 * 　"user_status" => ユーザステータス
	 * 　"bage_num" => リーダーモンスターのバッジ数,
	 * 　"badges" => リーダーモンスターの装着バッジ,
	 * )
	 * </code>
	 * @param array $party パーティ（t_raid_partyの1行に相当する連想配列）
	 * @param array $party_member パーティメンバー（t_raid_party_memberの1行に相当する連想配列）
	 * @param array $leader リーダー（Pp_MonsterManagerのgetActiveLeaderList関数で取得した配列中の1件）
	 * @return array ユーザー情報
	 */
	function convertUserInfoForApiResponse($party, $party_member, $leader)
	{
		$user_id = $party_member['user_id'];

		//マスタかメンバか
		if ($party['master_user_id'] == $user_id) $party_master = self::USER_TYPE_MASTER;
		else $party_master = self::USER_TYPE_MEMBER;

		$user_info = array(
			'user_id'              => $user_id,
			'user_name'            => $leader['name'],
			'user_rank'            => $leader['rank'],
			'leader_mons_id'       => $leader['monster_id'],
			'leader_mons_lv'       => $leader['lv'],
			'leader_mons_exp'      => $leader['exp'],
			'leader_mons_skill_lv' => $leader['skill_lv'],
			'user_type'            => $party_master,
			'user_status'          => $party_member['status'],
			'badge_num'            => $leader['badge_num'],
			'badges'               => $leader['badges'],
		);

		return $user_info;
	}

	/**
	 * メンバーの追加
	 * 
	 * @param int $party_id パーティID
	 * @param int $user_id ユーザーID
	 * 
	 * @return ユーザー識別コード
	 */
	private function _addMember( $party_id, $user_id )
	{
		// ハッシュ値の作成
		$seed = str_pad( $party_id, 12, '0', STR_PAD_LEFT ).str_pad( $user_id, 12, '0', STR_PAD_LEFT );
		$hash = hash( 'sha256', $seed );

		$param = array( $party_id, $user_id, self::MEMBER_STATUS_READY, $hash );
		$sql = "INSERT INTO t_raid_party_member( party_id, user_id, status, hash, date_created ) "
			 . "VALUES( ?, ?, ?, ?, NOW())";
		if( !$this->db_unit1->execute( $sql, $param ))
		{	// 書き込みエラー
			return null;
		}
		return $hash;
	}

	/**
	 * メンバー数を１人加算
	 * 
	 * @param int $party_id パーティID
	 * 
	 * @return true:正常終了, false:エラー
	 */
	/*
	private function _incMemberNum( $party_id )
	{
		$param = array( $party_id );
		$sql = "UPDATE t_raid_party "
			 . "SET member_num = member_num + 1, "
			 . "member_max = IF(( member_num <= member_max ), member_max, ( member_max + 1 )) "
			 . "WHERE party_id = ?";
		if( !$this->db_unit1->execute( $sql, $param ))
		{
			return false;
		}
		return true;
	}
	*/
	private function _setMemberNum( $party_id, $member_num )
	{
		error_log( "_setMemberNum( $party_id, $member_num )" );
		$param = array( $member_num, $party_id );
		$sql = "UPDATE t_raid_party "
			 . "SET member_num = ?, "
			 . "member_max = IF(( member_num <= member_max ), member_max, ( member_max + 1 )) "
			 . "WHERE party_id = ?";
		if( !$this->db_unit1->execute( $sql, $param ))
		{
			return false;
		}
		return true;
	}

}
?>
