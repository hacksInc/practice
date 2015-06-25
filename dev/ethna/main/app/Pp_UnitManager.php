<?php
/**
 *  Pp_UnitManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_UnitManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_UnitManager extends Ethna_AppManager
{

    static $current_unit = 0;

	/** memcacheのlifetime */
	const MEMCACHE_LIFETIME = 86400;
	
	/**
	 * DB接続(jm-ini.phpの'dsn_cmn'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_cmn = null;
	
	/**
	 * DB接続(jm-ini.phpの'dsn_cmn_r'で定義したDB)
	 * 
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_cmn_r = null;
	
//	/**
//	 * クライアント側アプリからリクエストされたユニットを取得する
//	 */
//	static function getRequestUnit()
//	{
//		if (function_exists('getallheaders')) {
//			$headers = getallheaders();
//			if (isset($headers['X-Jugmon-Unit']) && preg_match('/^[0-9]{1,10}$/', $headers['X-Jugmon-Unit'])) {
////				return $headers['X-Jugmon-Unit'];
//return '1';//暫定
//			}
//		}
//		
//		return null;
//	}
	
	/**
	 * 割り当て可能なユニットを取得する
	 * 
	 * @return int ユニット
	 */
	function getAllocatableUnit()
	{
		if( is_null( $this->db_cmn_r ))
		{	// インスタンスを取得していなければ取得
			$this->db_cmn_r =& $this->backend->getDB( 'cmn_r' );
		}
		// configから全ユニットの情報を取得
		$unit_all = $this->config->get( 'unit_all' );

		foreach( $unit_all as $unit => $columns )
		{
			// ユニットへの割り当て許可をチェック
			if( !isset( $columns['unit_allocatable'] ) || ( !$columns['unit_allocatable'] ))
			{	// このユニットへは割り当てが許可されていない
				$this->backend->logger->log( LOG_WARNING, "Unit is not allocatable." );
				continue;
			}
			
			if( !isset( $columns['max_unit_user'] ))
			{	// ユニットに割り当て可能な最大ユーザー数がない
				$this->backend->logger->log( LOG_WARNING, 'max_unit_user is not set.' );
				continue;
			}

			// ユニットの現在の所属人数を取得
			$param = array( $unit );
			$sql = "SELECT counter FROM ct_unit WHERE unit = ?";
			$counter = $this->db_cmn_r->GetOne( $sql, $param );

			// 所属人数から割り当てが可能かを判断
			if( is_numeric( $counter ) && ( $counter < $columns['max_unit_user'] ))
			{	// 割り当て可能
				return $unit;
			}
		}
		return null;
	}
	
	/**
	 * 引き継ぎIDからユニットを取得する
	 * 
	 * @param string $migrate_id 引き継ぎID
	 *
	 * @return int 所属ユニット
	 */
	function getUnitByMigrateId( $migrate_id )
	{
		if( !$this->db_cmn )
		{	// インスタンスを取得していなければ取得
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}

		$param = array( $migrate_id );
		$sql = "SELECT unit FROM ct_user_unit WHERE migrate_id = ?";

		return $this->db_cmn->GetOne( $sql, $param );
	}

	/**
	 * サイコパスIDからユニットを取得する
	 * 
	 * @param int $user_id
	 * @return int ユニット
	 */
	function getUnitByPpId( $pp_id )
	{
		if( is_null( $this->db_cmn ))
		{	// インスタンスを取得していなければ取得
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}

		$param = array( $pp_id );
		$sql = "SELECT unit FROM ct_user_unit WHERE pp_id = ?";

		return $this->db_cmn->GetOne( $sql, $param );
	}
	// 旧関数名互換のためのラッパー関数
	function getUnitFromUserId( $user_id )
	{
		return $this->getUnitByPpId( $user_id );
	}


	/**
	 * サイコパスIDからユニットを取得する（memcache優先）
	 * 
	 * @param int $user_id
	 * @return int ユニット
	 */
	function cacheGetUnitByPpId( $pp_id )
	{
		// キャッシュから取得
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_key = basename( BASE ).$pp_id.'UnitFromPpId';
		$unit = $cache_m->get( $cache_key, self::MEMCACHE_LIFETIME );
		if( is_numeric( $unit ))
		{
			return $unit;
		}

		// 取得できない場合はDBから取得し直す
		$unit = $this->getUnitByPpId( $pp_id );
		if( is_numeric( $unit ))
		{	// 正常に取得できたらキャッシュにセット
			$cache_m->set( $cache_key, $unit );
		}
		else
		{	// 取得エラー
			$this->backend->logger->log( LOG_ERR, 'cacheGetUnitFromPpId failed.' );
		}
		return $unit;
	}
	// 旧関数名互換のためのラッパー関数
	function cacheGetUnitFromUserId( $user_id )
	{
		return $this->cacheGetUnitByPpId( $user_id );
    }


    /**
     * ユーザーIDリストから所属ユニット単位でユーザーIDを返す
     */
    function cacheGetUnitFromUserIdList($user_id_list) {
        $unit_info = array();
		
        foreach ($user_id_list as $user_id) {
            $unit = $this->cacheGetUnitFromUserId($user_id);
            $unit_info[$unit][] = $user_id;
        }
        return $unit_info;
	}

	/**
	 * ユニット情報を再セットする
	 *
	 * @param int $unit ユニット番号
	 *
	 * @return boolean true:正常終了, false:エラー
	 */
	function resetUnit( $unit )
	{
		// configから全ユニットの情報を取得
		$unit_all = $this->config->get( 'unit_all' );
		if( !isset( $unit_all[$unit] ))
		{	// 設定にないユニットを指定している！？
			$this->backend->logger->log( LOG_WARNING, 'Invalid unit.' );
			return false;
		}

		// 使用ユニットを再設定
		$this->config->set( 'unit_id', $unit );	// config
		$this->current_unit = $unit;			// 現在使用中のユニット

		$unit_info = $unit_all[$unit];
		foreach( $unit_info as $k => $v )
		{
			$this->config->set( $k, $v );
		}

		// 無理矢理だが... DSNを再セット
		$this->backend->ctl->resetDSN();

		return true;
	}

    /**
     * すべてのユニットに対してGetAllメソッドを実行する
     * @param $sql
     * @param $param
     * @param $unit 指定ユニットに対して実行
     * @param bool SQL_BIG_SELECTをONにする
     * @return Array
     */
    function getAllMultiUnit($sql, $param, $unit = NULL, $isBigSelect = false) {

        $unit_all = $this->backend->config->get('unit_all');

        if (!empty($unit)) {
            if ($unit == $this->current_unit) {
                if ($isBigSelect) {
                    $this->backend->getDB()->query("SET SESSION sql_big_selects = ON");
                }
                $rows = $this->backend->getDB()->GetAll($sql, $param);
                return $rows;
            } else {
                $unit_all = $this->backend->config->get('unit_all');
                $con = NewADOConnection($unit_all[$unit]['dsn']);
                if ($isBigSelect) {
                    $con->Execute("SET SESSION sql_big_selects = ON");
                }
                $con->Execute("SET NAMES utf8mb4");
                $rows = $con->GetAll($sql, $param);
                if (is_bool($rows) && !$rows) {
                    return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                        $con->ErrorNo(), $con->ErrorMsg(), __FILE__, __LINE__);
                }
                $con->close();
                return $rows;
            }
        } else {
            $ary = array();

            foreach($unit_all as $unit_no => $unit_info) {
                if ($this->current_unit == $unit_no) {
                    if ($isBigSelect) {
                        $this->backend->getDB()->query("SET SESSION sql_big_selects = ON");
                    }
                    $rows = $this->backend->getDB()->GetAll($sql, $param);
                    if (is_bool($rows) && !$rows) {
                        return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                            $this->backend->getDB()->db->ErrorNo(), $this->backend->getDB()->db->ErrorMsg(), __FILE__, __LINE__);
                    }
                    $ary = array_merge($ary, $rows);
                } else { // 別ユニットの場合はコネクションを直接接続
                    $con = NewADOConnection($unit_info['dsn']);
                    $con->Execute("SET NAMES utf8mb4");
                    if ($isBigSelect) {
                        $con->Execute("SET SESSION sql_big_selects = ON");
                    }
                    $rows = $con->GetAll($sql, $param);
                    if (is_bool($rows) && !$rows) {
                        return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                            $con->ErrorNo(), $con->ErrorMsg(), __FILE__, __LINE__);
                    }
                    $ary = array_merge($ary, $rows);
                    $con->close();
                }
            }
        }
        return $ary;
    }

    /**
     * 指定ユニットに対してGetAllメソッドを実行する
     */
    function getAllSpecificUnit($sql, $param, $unit, $isBigSelect = false) {
        if ($unit == $this->current_unit) {
            if ($isBigSelect) {
                $this->backend->getDB()->query("SET SESSION sql_big_selects = ON");
            }
            $rows = $this->backend->getDB()->GetAll($sql, $param);
            if (is_bool($rows) && !$rows) {
                return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                    $this->backend->getDB()->db->ErrorNo(), $this->backend->getDB()->db->ErrorMsg(), __FILE__, __LINE__);
            }
            return $rows;
        } else {
            $unit_all = $this->backend->config->get('unit_all');
            $con = NewADOConnection($unit_all[$unit]['dsn']);
            $con->Execute("SET NAMES utf8mb4");
            if ($isBigSelect) {
                $con->Execute("SET SESSION sql_big_selects = ON");
            }
            $rows = $con->GetAll($sql, $param);
            if (is_bool($rows) && !$rows) {
                return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                    $con->ErrorNo(), $con->ErrorMsg(), __FILE__, __LINE__);
            }
            $con->close();
            return $rows;
        }
    }

    /**
     * 複数のユニットに対してGetRowクエリを実行する
     * @param $sql
     * @param $param
     * @return null
     */
    function getRowMultiUnit($sql, $param, $unit = NULL, $isBigSelect = false) {
        $row = NULL;
        $unit_all = $this->backend->config->get('unit_all');

        if (!empty($unit)) {
            if ($unit == $this->current_unit) {
                if ($isBigSelect) {
                    $this->backend->getDB()->query("SET SESSION sql_big_selects = ON");
                }
                return $this->backend->getDB()->GetRow($sql, $param);
            } else {
                $con = NewADOConnection($unit_all[$unit]['dsn']);
                $con->Execute("SET NAMES utf8mb4");
                if ($isBigSelect) {
                    $con->Execute("SET SESSION sql_big_selects = ON");
                }
                $row = $con->GetRow($sql, $param);
                if (is_bool($row) && !$row) {
                    return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                        $con->ErrorNo(), $con->ErrorMsg(), __FILE__, __LINE__);
                }
                $con->close();
                return $row;
            }
        } else {
            foreach($unit_all as $unit_no => $unit_info) {
                if ($this->current_unit == $unit_no) {
                    if ($isBigSelect) {
                        $this->backend->getDB()->query("SET SESSION sql_big_selects = ON");
                    }
                    $row = $this->backend->getDB()->GetRow($sql, $param);
                    if (is_bool($row) && !$row) {
                        return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                            $this->backend->getDB()->db->ErrorNo(), $this->backend->getDB()->db->ErrorMsg(), __FILE__, __LINE__);
                    }
                    if (count($row) > 0) break;
                } else { // 別ユニットの場合はコネクションを直接接続
                    $con = NewADOConnection($unit_info['dsn']);
                    $con->Execute("SET NAMES utf8mb4");
                    if ($isBigSelect) {
                        $con->Execute("SET SESSION sql_big_selects = ON");
                    }
                    $row = $con->GetRow($sql, $param);
                    if (is_bool($row) && !$row) {
                        return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                            $con->ErrorNo(), $con->ErrorMsg(), __FILE__, __LINE__);
                    }
                    $con->close();
                    if (count($row) > 0) break;
                }
            }
        }
        return $row;
    }


    /**
     * 複数のユニットに対してGetOneクエリを実行する
     * @param $sql 実行SQL
     * @param $param SQLパラメータ
     * @return Array
     */
    function getOneMultiUnit($sql, $param, $unit = NULL, $isBigSelect = false) {
        $one = NULL;
        $unit_all = $this->backend->config->get('unit_all');

        if (!empty($unit)) {
            if ($unit == $this->current_unit) {
                if ($isBigSelect) {
                    $this->backend->getDB()->query("SET SESSION sql_big_selects = ON");
                }
                $one = $this->backend->getDB()->getOne($sql, $param);
                if (is_bool($one) && !$one) {
                    return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                        $this->backend->getDB()->db->ErrorNo(), $this->backend->getDB()->db->ErrorMsg(), __FILE__, __LINE__);
                }
                return $one;
            } else {
                $con = NewADOConnection($unit_all[$unit]['dsn']);
                $con->Execute("SET NAMES utf8mb4");
                if ($isBigSelect) {
                    $con->Execute("SET SESSION sql_big_selects = ON");
                }
                $one = $con->getOne($sql, $param);
                if (is_bool($one) && !$one) {
                    return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                        $con->ErrorNo(), $con->ErrorMsg(), __FILE__, __LINE__);
                }
                $con->close();
                return $one;
            }
        } else {
            foreach($unit_all as $unit_no => $unit_info) {
                if ($this->current_unit == $unit_no) {
                    if ($isBigSelect) {
                        $this->backend->getDB()->query("SET SESSION sql_big_selects = ON");
                    }
                    $one = $this->backend->getDB()->GetOne($sql, $param);
                    if (is_bool($one) && !$one) {
                        return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                            $this->backend->getDB()->db->ErrorNo(), $this->backend->getDB()->db->ErrorMsg(), __FILE__, __LINE__);
                    }
                    if (count($one) > 0) break;
                } else { // 別ユニットの場合はコネクションを直接接続
                    $con = NewADOConnection($unit_info['dsn']);
                    $con->Execute("SET NAMES utf8mb4");
                    if ($isBigSelect) {
                        $con->Execute("SET SESSION sql_big_selects = ON");
                    }
                    $one = $con->GetOne($sql, $param);
                    if (is_bool($one) && !$one) {
                        return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
                            $con->ErrorNo(), $con->ErrorMsg(), __FILE__, __LINE__);
                    }
                    if (count($one) > 0) break;
                    $con->close();
                }
            }
        }
        return $one;
    }

    /**
     * ユニットを指定してExecuteクエリを実行します
     * @param $unit
     * @param $sql
     * @param $param
     * @return mixed
     */
    function executeForUnit($unit, $sql, $param, $isBigSelect = false) {
        if ($this->current_unit == $unit) {
            $ret = $this->backend->getDB()->execute($sql, $param);
            if ($ret) {
                if ($isBigSelect) {
                    $this->backend->getDB()->query("SET SESSION sql_big_selects = ON");
                }
                $ret->affected_rows = $this->backend->getDB()->db->affected_rows();
                $insert_id = $this->backend->getDB()->db->Insert_ID();
                if (is_null( $insert_id ) === false) {
                    $ret->insert_id = $insert_id;
                }
                return $ret;
            } else {
                $err = new stdClass();
                $err->ErrorNo = $this->backend->getDB()->db->ErrorNo();
                $err->ErrorMsg = $this->backend->getDB()->db->ErrorMsg();
                return $err;
            }
        } else {
            $unit_all = $this->backend->config->get('unit_all');

            $unit_info = $unit_all[$unit];
            $con = NewADOConnection($unit_info['dsn']);
            $con->Execute("SET NAMES utf8mb4");
            if ($isBigSelect) {
                $con->Execute("SET SESSION sql_big_selects = ON");
            }
            $ret = $con->Execute($sql, $param);
            if ($ret) {
                $ret->affected_rows = $con->affected_rows();
                $insert_id = $con->Insert_ID();
                if (is_null($insert_id) === false) {
                    $ret->insert_id = $insert_id;
                }
                $con->close();
                return $ret;
            } else {
                $err = new stdClass();
                $err->ErrorNo = $con->ErrorNo();
                $err->ErrorMsg = $con->ErrorMsg();
                return $err;
            }
        }
    }

    /**
     * ユニットをまたぐユーザーIDリストからそれぞれのユニットを判別してクエリを実行します
     * @param $user_id_list
     * @param $sql
     * @param $param
     * @return array
     */
    function getAllMultiUnitFromUserIdList($sql, $param, $user_id_list) {
        $ary = array();
        // ユーザー一覧から別ユニットへの参照が必要かをチェックする
        $unit_user_list = $this->cacheGetUnitFromUserIdList($user_id_list);
        foreach($unit_user_list as $unit => $user_ids) {
            $rows = $this->getAllSpecificUnit($sql, $user_ids, $unit, false);
            $ary = array_merge($ary, $rows);
        }
        return $ary;
    }

    /**
     * ユニット情報を取得
     * @param $unit 取得したいユニット
     * @return array
     */
	function getUnitInfo( $unit = null )
	{
		$unit_all = $this->config->get('unit_all');
		if( is_null( $unit ) === true )
		{	// 全ユニットの情報を返す
			return $unit_all;
		}
		else if( array_key_exists( $unit, $unit_all ) === true )
		{	// 指定のユニットの情報があればそれを返す
			return $unit_all[$unit];
		}
		// 指定のユニット情報がない
		return null;
	}
}
?>
