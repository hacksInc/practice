<?php
/**
 *  Pp_PresentManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_PresentManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_PresentManager extends Ethna_AppManager
{
	/**
	 * DB接続(pp-ini.phpの'dsn_cmn'で定義したDB)
	 *
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_cmn = null;

	const CATEGORY_ITEM		= 1;	// アイテム
	const CATEGORY_PHOTO	= 2;	// フォト
	const CATEGORY_PP		= 3;	// ポータルポイント

	const TYPE_ITEM          = 1;	// 通常アイテム

	const ID_NEW_PRESENT    = -1;	// プレゼント新規登録時に指定するプレゼントID
	const PPID_FROM_ADMIN   = -1;	// 運営からプレゼントする際のユーザID

	const REWARD_TYPE_ITEM = 1;
	const REWARD_TYPE_MONSTER = 3;

	// status 0:新規 1:開封済 2:受取済 -1:削除済
	// ユーザごと制限数
	const MAX_NUMBER = 100;
	// プレゼント受取期限　送信日時から
	const LIMIT_RECEIVE_HOUR = 2400;	// 非アクティブ判定日数×24時間
	const STATUS_DELETE = -1;	// 削除済み
	const STATUS_NEW =     0;	// 新規
	const STATUS_OPEN =    1;	// 開封済み
	const STATUS_RECEIVE = 2;	// 受取済み

	// コメント定型文のID
	const COMMENT_FREEWORD    = 0;	// 自由文
	const COMMENT_APOLOGY     = 1;	// 障害・不具合のお詫び
	const COMMENT_PRESENT     = 2;	// 運営からのプレゼント
	const COMMENT_SERIALCODE  = 3;	// シリアルコード特典
	const COMMENT_ACHIEVEMENT = 4;	// MEDAL達成報酬
	const COMMENT_TWITTER     = 5;	// SNS報酬（フォロワー突破）
	const COMMENT_EVENT       = 6;	// イベント報酬

	const DIST_STATUS_START = 0;	// 配布開始
	const DIST_STATUS_STOP  = 1;	// 配布中止

	const TARGET_TYPE_ALL    = 0;	// 全ユーザー
	const TARGET_TYPE_TERM   = 1;	// 指定期間アクセスユーザー
	const TARGET_TYPE_PPID   = 2;	// サイコパスID指定

	var $COMMENT_ID_OPTIONS = array(
		//0 => '自由文', // サイコ不必要なのでコメントアウト
		1 => '障害・不具合のお詫びです',
		2 => '運営からのプレゼントです',
		3 => 'シリアルコード特典です',
		4 => 'MEDAL達成報酬です',
		5 => 'Follower突破記念です',
		6 => 'イベント報酬です',
	);

	/**
	 *  コンストラクタ
	 */
	function __construct(&$backend) {
		parent::__construct($backend);

		// DBのインスタンスを生成
		if( is_null( $this->db_cmn ))
		{	// インスタンスを取得していないなら取得
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}
	}

	public function getStatusName($status)
	{
		switch((int)$status)
		{
			case self::STATUS_DELETE :
				return '削除済';
				break;

			case self::STATUS_NEW :
				return '新規';
				break;

			case self::STATUS_OPEN :
				return '開封済';
				break;

			case self::STATUS_RECEIVE :
				return '受取済';
				break;

			default:
				return '';
				break;
		}
	}

	/**
	 * カテゴリータイプを取得する
	 *
	 * @param int $present_category
	 * @return int
	 */
	function getCategoryType( $present_category )
	{
		$item_m =& $this->backend->getManager('Item');
		switch ( $present_category )
		{
			case $item_m->ITEM_ID_PHOTO_FILM:
			case $item_m->ITEM_ID_THERAPY_TICKET:
			case $item_m->ITEM_ID_RESERVE_DOMINATOR:
			case $item_m->ITEM_ID_DRONE:
				$category_type = self::CATEGORY_ITEM;
				break;
			case $item_m->ITEM_ID_PORTAL_POINT:
				$category_type = self::CATEGORY_PP;
				break;
			case $item_m->ITEM_ID_PHOTO:
				$category_type = self::CATEGORY_PHOTO;
				break;
		}
		return $category_type;
	}

	/**
	 * キャッシュ名を取得する
	 *
	 * @param string $key
	 * @param array $array
	 * @return array
	 */
	function getCacheKey( $key, $id )
	{
		if ( empty( $key ) || empty( $id ))
		{
			return null;
		}

		$cache_key = "";

		switch ( $key )
		{
		case "pp_id":
			$cache_key = "ct_user_present_list__{$id}";
			break;
		}

		return $cache_key;
	}

	/**
	 * プレゼント情報を取得する
	 *
	 * @param int $present_id
	 * @return array
	 */
	function getUserPresent( $present_id )
	{
		if ( empty( $present_id ))
		{	// プレゼントIDがなければ取得エラー
			return null;
		}

		// 取得できない場合はDBから取得
		$param = array( $present_id );
		$sql = "SELECT * FROM ct_user_present"
			. " WHERE present_id = ?";
		$data = $this->db_cmn->GetRow( $sql, $param );

		return $data;
	}

	/**
	 * 指定されたプレゼント配布管理IDのプレゼント情報を取得する
	 *
	 * @param int $pp_id サイコパスID
	 * @param int $present_mng_id プレゼント管理ID
	 * @return array
	 */
	function getUserPresentmngid( $pp_id, $present_mng_id )
	{
		if ( empty( $pp_id )  or empty( $present_mng_id ))
		{	// サイコパスID or プレゼント管理IDがなければ取得エラー
			return null;
		}

		// 取得できない場合はDBから取得
		$param = array( $pp_id, $present_mng_id );
		$sql = "SELECT * FROM ct_user_present"
			. " WHERE pp_id = ? AND present_mng_id = ?";
		$data = $this->db_cmn->GetRow( $sql, $param );

		return $data;
	}

	/**
	 * 指定されたプレゼント配布管理IDのプレゼント情報を取得する(複数版)
	 *
	 * @param int $pp_id サイコパスID
	 * @param array $present_mng_ids プレゼント管理IDのリスト
	 * @return array
	 */
	function getUserPresentmngids( $pp_id, $present_mng_ids )
	{
		$param = array( $pp_id );
		$where_present_msg_id_in = array();

		// present_mng_ids配列から個数分 IN クエリ用配列を作成
		foreach ( $present_mng_ids as $id )
		{
			$param[] = $id;
			$where_present_msg_id_in[] = "?";
		}

		// クエリ作成
		$sql = "SELECT * FROM ct_user_present"
			. " WHERE pp_id = ? AND present_mng_id IN (" . implode(',', $where_present_msg_id_in) . ")";

		// 実行
		return $this->db_cmn->GetAll( $sql, $param );
	}

	/**
	 * 期間超過したプレゼントを削除する
	 *
	 * @param int $pp_id
	 * @param int $limit
	 * @return array
	 */
	function deleteExpiredUserPresent( $pp_id )
	{
		// 受取制限時間を超えたものは削除フラグを立てる
		// 分は無視。時のみで計算
		$date_limit = date( 'Y-m-d H:59:59', strtotime('-' . self::LIMIT_RECEIVE_HOUR . ' hours', strtotime(date('Y-m-d H:0:0'))));
		$param = array( $pp_id, $date_limit, self::STATUS_NEW );
		$sql = "SELECT * FROM ct_user_present WHERE pp_id = ? AND date_created <= ? AND status = ?";
		$result = $this->db_cmn->execute( $sql, $param );
		if ( !$result ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}

		// 削除フラグ対象となる場合は削除フラグ立てる
		$logs = '';
		if ( $result->RecordCount() > 0 ) {
			$logs = $result->GetArray();
			$ids = array();
			foreach( $logs as $value ) {
				$ids[] = $value['present_id'];
			}
			$param = array( self::STATUS_DELETE );
			$sql = "UPDATE ct_user_present SET status = ? WHERE present_id IN (" . implode(",", $ids) . ")";
			if ( !$this->db_cmn->execute( $sql, $param )) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}

		return $logs;
	}

	/**
	 * プレゼント保持最大数を超過したプレゼントを削除する
	 *
	 * @param int $pp_id
	 * @return array
	 */
	function deleteMaxOverUserPresent( $pp_id )
	{
		$offset = 0;
		$limit = self::MAX_NUMBER;

		// 件数取得
		$param = array( $pp_id, self::STATUS_NEW );
		$sql = "SELECT * FROM ct_user_present WHERE pp_id = ? AND status = ? ORDER BY present_id";
		$ret = $this->db_cmn->GetAll( $sql, $param );
		if ( $ret === false ) {
			return false;
		}
		$present_count = count($ret);

		$delete_present_data = '';
		if ( $present_count > $limit ) {
			$delete_count = $present_count - $limit;
			$param = array( self::STATUS_DELETE, $pp_id, self::STATUS_NEW, ($present_count - $limit));
			$sql = "UPDATE ct_user_present SET status = ? WHERE pp_id = ? AND status = ? ORDER BY present_id LIMIT ?";
			if ( !$this->db_cmn->execute( $sql, $param )) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
			}
			$cnt = 0;
			foreach ( $ret as $k => $v ) {
				$delete_present_data[] = $v;
				$cnt++;
				if ( $cnt >= $delete_count ) {
					break;
				}
			}
		}

		return $delete_present_data;
	}

	/**
	 * プレゼント一覧を取得する
	 *
	 * @param int $pp_id
	 * @return array
	 */
	function getUserPresentList( $pp_id )
	{
		if ( empty( $pp_id ))
		{	// サイコパスIDがなければ取得エラー
			return null;
		}

		// 受取リスト
		$param = array( self::STATUS_NEW, $pp_id );
		$sql = "SELECT * FROM ct_user_present"
			. " WHERE status = ? AND pp_id = ?"
			. " ORDER BY present_id DESC";
		return $this->db_cmn->GetAll( $sql, $param );
	}

	/**
	 * プレゼント情報を取得する（ID指定、複数）
	 *
	 * @param array $present_ids プレゼントID配列
	 * @return array 取得データ
	 */
	function getUserPresentListByPresentIds ( $present_ids )
	{
		if ( count( $present_ids ) == 0 ) return array();

		$target = array();
		$param = array();
		foreach ( $present_ids as $id ) {
			$target[] = "?";
			$param[] = $id;
		}

		$sql = "SELECT * FROM ct_user_present"
			. " WHERE present_id IN ( %s )"
			. " ORDER BY present_id DESC";

		return $this->db_cmn->GetAll( sprintf( $sql, implode( ",", $target ) ), $param );
	}

	/**
	 * ユーザーごと未受け取り数
	 *
	 * @param int $pp_id  サイコパスID
	 * @return int $count 件数
	 */
	public function newPresentCount( $pp_id ) {
		$param = array( $pp_id, self::STATUS_NEW );
		$sql = "SELECT COUNT(present_id) FROM ct_user_present WHERE pp_id = ? AND status = ?";
		$newCount = $this->db_cmn->GetOne( $sql, $param );
		if ( $newCount === false ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return $newCount;
	}

	/**
	 * プレゼントステータス変更
	 *
	 * @param string $present_id  プレゼントID
	 * @param int $status  ステータス
	 * @return array
	 */
	public function changePresentStatus ( $present_id, $status )
	{
		$param = array( $status, $present_id );
		$sql = "UPDATE ct_user_present SET"
			. " status = ?"
			. " WHERE present_id = ?";
		if ( !$this->db_cmn->execute( $sql, $param )) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}

		return true;
	}

	/**
	 * プレゼント配布情報をセットする
	 *
	 * @param int $pp_id
	 * @param int $present_id
	 * @param array $columns セットする情報の連想配列
	 * @param int $unit  アクセス対象ユニット(指定がない場合はカレントユニット)
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setUserPresent( $pp_id, $present_id, $columns, $unit = null )
	{
		if (!$this->db_cmn) {
			$this->db_cmn =& $this->backend->getDB('cmn');
		}

		if ( !isset($columns['present_mng_id'] )) {
			$columns['present_mng_id'] = -1;
		}

		$param = array(
			$columns['present_mng_id'],
			$pp_id,
			$columns['comment_id'],
			$columns['present_category'],
			$columns['present_value'],
			$columns['num'],
			self::STATUS_NEW,
			date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])
		);
		$sql = "INSERT INTO ct_user_present(
			present_mng_id,
			pp_id,
			comment_id,
			present_category,
			present_value,
			num,
			status,
			date_created)"
			. " VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
		if ( is_null($unit) === false ) {
			$unit_m = $this->backend->getManager( 'Unit' );
			$res = $unit_m->executeForUnit( $unit, $sql, $param, false );
			if ( $res->ErrorNo ) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$res->ErrorNo, $res->ErrorMsg, __FILE__, __LINE__);
			}
			$id = $res->insert_id;

		} else {
			$res = $this->db_cmn->execute( $sql, $param );
			if ( $res === false ) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
			}
			$id = $this->db_cmn->db->Insert_ID();
		}

		return $id;
	}

	/**
	 * プレゼント受取情報をセットする
	 *
	 * @param int $pp_id
	 * @param int $present_id
	 * @param array $columns セットする情報の連想配列
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setReceiptUserPresent( $pp_id, $present_id, $columns )
	{
		$affected_rows = 0;
		// UPDATE実行
		$param = array_values( $columns );
		$param[] = $present_id;
		$sql = "UPDATE ct_user_present SET "
			. implode("=?,", array_keys($columns)) . "=? "
			. " WHERE present_id = ?";
		if ( !$this->db_cmn->execute( $sql, $param )) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}

		// 影響した行数を確認
		// present_idはPKなので、この判定はありえない
/*        $affected_rows = $this->db_cmn->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
}*/

		return true;
	}

	/**
	 * プレゼント情報を削除する
	 *
	 * @param int $present_id
	 * @param int $pp_id
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function deleteUserPresent( $present_id, $pp_id = null )
	{
		$param = array( $present_id );
		$sql = "DELETE FROM ct_user_present"
			. " WHERE present_id = ?";

		if ($pp_id) {
			$param[] = $pp_id;
			$sql .= " AND pp_id = ?";
		}

		//error_log('DEBUG:' . basename(__FILE__) . ':' . __LINE__ . ':' . implode(",", $param));
		if (!$this->db_cmn->execute( $sql, $param )) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		$affected_rows = $this->db_cmn->db->affected_rows();
		if ( $affected_rows != 1 ) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		return true;
	}

	/**
	 * 指定ユーザのプレゼントを全て削除状態にする
	 *
	 * @param int $pp_id
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function deleteUserAllPresentStatus( $pp_id )
	{
		$param = array( self::STATUS_DELETE, $pp_id );
		$sql = "UPDATE ct_user_present SET status = ? "
			. " WHERE pp_id = ?";

		if ( !$this->db_cmn->execute( $sql, $param )) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		$affected_rows = $this->db_cmn->db->affected_rows();
		if ( $affected_rows != 1 ) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		return true;
	}

	/**
	 * プレゼント配布管理情報を取得する
	 *
	 * @param int $present_mng_id
	 * @return array
	 */
	function getPresentMng( $present_mng_id )
	{
		$param = array( $present_mng_id );
		$sql = "SELECT * FROM ct_present_distribution"
			. " WHERE present_mng_id = ?";
		return $this->db_cmn->GetRow( $sql, $param );
	}

	/**
	 * 配布待ちステータスのプレゼント配布管理情報を取得する
	 *
	 * @return array
	 */
	function getPresentMngWait()
	{
		$param = array( self::DIST_STATUS_START, date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
		$sql = "SELECT * FROM ct_present_distribution"
			. " WHERE status = ? AND distribute_date_start <= ? ORDER BY distribute_date_start ASC";
		return $this->db_cmn->GetRow( $sql, $param );
	}

	/**
	 * プレゼント配布管理情報一覧を取得する
	 *
	 * @return array
	 */
	function getPresentMngList( $offset = 0, $limit = 10 )
	{
		$param = array( $offset, $limit );
		$sql = "SELECT * FROM ct_present_distribution ORDER BY present_mng_id DESC LIMIT ?, ?";
		$result = $this->db_cmn->execute( $sql, $param );
		if ( !$result ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return $this->db_cmn->GetAll( $sql, $param );
	}

	/**
	 * 期間内でstatus=0のプレゼント配布管理情報一覧を取得する
	 *
	 * @return array
	 */
	function getPresentMngListTerm()
	{
		$param = array( self::DIST_STATUS_START, date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']), date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
		$sql = "SELECT * FROM ct_present_distribution WHERE status = ? AND distribute_date_start <= ? AND distribute_date_end > ? ORDER BY present_mng_id ASC";
		$result = $this->db_cmn->execute( $sql, $param );
		if ( !$result ) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return $this->db_cmn->GetAll( $sql, $param );
	}

	/**
	 * プレゼント配布管理情報をセットする
	 *
	 * @param int $present_mng_id
	 * @param array $columns セットする情報の連想配列
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setPresentMng( $present_mng_id, $columns )
	{
		$affected_rows = 0;
		// UPDATE実行
		$param = array_values($columns);
		$param[] = $present_mng_id;
		$sql = "UPDATE ct_present_distribution SET "
			. implode("=?,", array_keys($columns)) . "=? "
			. " WHERE present_mng_id = ?";
		if (!$this->db_cmn->execute( $sql, $param )) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db_cmn->db->affected_rows();
		if ( $affected_rows > 1 ) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		if ( $affected_rows == 0 ) {
			// INSERT実行
			$param = array(
				$columns['target_type'],
				$columns['access_date_start'],
				$columns['access_date_end'],
				$columns['pp_id'],
				$columns['comment_id'],
				$columns['comment'],
				$columns['present_category'],
				$columns['present_value'],
				$columns['lv'],
				$columns['num'],
				$columns['status'],
				$columns['distribute_user_total'],
				$columns['distribute_date_start'],
				$columns['distribute_date_end'],
				$columns['account_regist'],
				$columns['account_update'],
				date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
			$sql = "INSERT INTO ct_present_distribution(
				target_type,
				access_date_start,
				access_date_end,
				pp_id,
				comment_id,
				comment,
				present_category,
				present_value,
				lv,
				num,
				status,
				distribute_user_total,
				distribute_date_start,
				distribute_date_end,
				account_regist,
				account_update,
				date_created)"
				. " VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			if ( !$this->db_cmn->execute( $sql, $param )) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}
		return true;
	}

	function insertPresentMng( $present_mng_id, $columns )
	{
		// INSERT実行
		$param = array(
			$columns['target_type'],
			$columns['access_date_start'],
			$columns['access_date_end'],
			$columns['pp_id'],
			$columns['comment_id'],
			$columns['comment'],
			$columns['present_category'],
			$columns['present_value'],
			$columns['lv'],
			$columns['num'],
			$columns['status'],
			$columns['distribute_user_total'],
			$columns['distribute_date_start'],
			$columns['distribute_date_end'],
			$columns['account_regist'],
			$columns['account_update'],
			date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
		$sql = "INSERT INTO ct_present_distribution(
			target_type,
			access_date_start,
			access_date_end,
			pp_id,
			comment_id,
			comment,
			present_category,
			present_value,
			lv,
			num,
			status,
			distribute_user_total,
			distribute_date_start,
			distribute_date_end,
			account_regist,
			account_update,
			date_created)"
			. " VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		if ( !$this->db_cmn->execute( $sql, $param )) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	function updatePresentMng( $present_mng_id, $columns )
	{
		$affected_rows = 0;
		// UPDATE実行
		$param = array_values($columns);
		$param[] = $present_mng_id;
		$sql = "UPDATE ct_present_distribution SET "
			. implode("=?,", array_keys($columns)) . "=? "
			. " WHERE present_mng_id = ?";
		if ( !$this->db_cmn->execute( $sql, $param )) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db_cmn->db->affected_rows();
		if ( $affected_rows > 1 ) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		return true;
	}

	function incPresentMngCnt( $present_mng_id )
	{
		$affected_rows = 0;
		// UPDATE実行
		$param = array( $present_mng_id );
		$sql = "UPDATE ct_present_distribution SET distribute_user_total=distribute_user_total+1"
			. " WHERE present_mng_id = ?";
		if (!$this->db_cmn->execute( $sql, $param )) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}
		return true;
	}

	/**
	 * 渡されたct_user_presentのデータをuser_box形式に変換する
	 */
	function convertUserBox ( $user_present )
	{
		$user_box = array();

		if( !empty( $user_present ))
		{
			foreach ( $user_present as $key => $row ) {
				$user_box[] = array(
					"present_id"		=> $row['present_id'],
					"comment"			=> $this->COMMENT_ID_OPTIONS[$row['comment_id']],
					"present_category"	=> $row['present_category'],
					"present_value"		=> $row['present_value'],
					"num"				=> $row['num'],
					"date_created"		=> $row['date_created'],
				);
			}
		}

		return $user_box;
	}
}

