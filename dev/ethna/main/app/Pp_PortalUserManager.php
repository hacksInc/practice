<?php
/**
 *  Pp_PortalUserManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once "Pp_UserManager.php";
require_once "Pp_Define.php";

/**
 *  Pp_PortalUserManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_PortalUserManager extends Pp_UserManager
{
	protected $db_log = null;
	
	/**
	 * ログイン実処理
	 * ログインチェックの必要は現状ここにしかないので、別途関数は作らない
	 */
	function execLogin ( $pp_id, &$login_now )
	{
		$date = date( "Y-m-d" );
		
		// 当日ログインしているかチェック
		$param = array( $pp_id, $date );
		$sql = "SELECT date FROM ut_portal_login_history WHERE pp_id = ? AND date = ?";
		$row = $this->db->GetRow( $sql, $param );
		
		if ( Ethna::isError( $row ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		// ログイン済みならtrue
		if ( isset( $row['date'] ) ) return true;
		
		// トランザクション開始
		$commit = true;
		$this->db->begin();
		
		// ログイン情報を記録
		$param = array( $pp_id, $date );
		$sql = "INSERT INTO ut_portal_login_history( pp_id, date, date_created ) VALUES( ?, ?, NOW() )";
		if ( $commit ) {
			if ( !$this->db->execute( $sql, $param ) ) {
				Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
				$commit = false;
			}
		}
		
		// ポイントを付与
		if ( $commit ) {
			if ( Ethna::isError( $this->addPoint( $pp_id, 10, "ログインボーナス" ) ) ) {
				$commit = false;
			}
		}
		
		// ログイン回数を更新
		$param = array( $pp_id );
		$sql = "UPDATE ut_portal_user_base SET login_num = login_num + 1, date_modified = NOW() WHERE pp_id = ?";
		if ( $commit ) {
			if ( !$this->db->execute( $sql, $param ) ) {
				Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
				$commit = false;
			}
		}
		
		// ログイン処理完了
		if ( $commit ) $login_now = true;
		
		if ( $commit )	$this->db->commit();
		else			$this->db->rollback();
		
		return $commit;
	}
	
	/**
	 * 旧ユーザーを検索
	 * 
	 * @param string $email Eメールアドレス（旧サイコパスID）
	 * @param string $password パスワード（旧パスワード）
	 * @param int $device_type 引継ぎ先の端末タイプ（1:iOS、2:Android）
	 * @return bool|object 処理結果
	 */
	function isOldUser ( $email, $password, $device_type )
	{
		// 旧ユーザーはiOSへは直接引継ぎ不可（旧来の仕様）
		if ( $device_type == 1 ) {
			return Ethna::raiseError( "not convert android to ios FILE[%s] LINE[%d]", E_USER_ERROR, __FILE__, __LINE__ );
		}
		
		// どちらかが空ならraiseError
		if ( is_null( $email ) || $email == '' || is_null( $password ) || $password == '' ) {
			return Ethna::raiseError( "need email and password FILE[%s] LINE[%d]", E_USER_ERROR, __FILE__, __LINE__ );
		}
		
		$param = array( $email, $this->getEncryptedPassword( $password ) );
		$sql = "SELECT * FROM ut_portal_user_base WHERE email = ? AND password = ?";
		
		$row = $this->db->GetRow( $sql, $param );
		
		// この関数においてエラーとレコードがないのは違うので分ける
		if ( Ethna::isError( $row ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		if ( !$row ) return false;
		
		return true;
	}
	
	/**
	 * 旧ユーザー（v1.0.6以下）のコンバート
	 *
	 * @param int $pp_id サイコパスID
	 * @param string $uuid UUID
	 * @param string $device_type 端末タイプ（1:iOS、2:Android）
	 * @param string $email メールアドレス（旧サイコパスID）
	 * @param string $password パスワード文字列
	 * @return array|bool
	 */
	function convertOldUser ( $pp_id, $uuid, $device_type, $email, $password )
	{
		// 旧ユーザーについてはゲームデータ等はバッチ処理で生成済み
		// パスワード、引継ぎID発行を行い、ut_portal_user_baseからemail、passwordを削除する
		// 旧端末からの引継ぎは一回だけ行える
		$unit_m =& $this->backend->getManager( "Unit" );
		
		// 旧ユーザーは全員ユニット1
		if ( $unit_m->current_unit != 1 ) $unit_m->resetUnit( 1 );
		
		// トランザクション
		$commit = true;
		$this->db->begin();
		
		// インストールパスワードを取得
		$install_pw = $this->newInstallPassword();

		// 引継ぎ情報を作成する
		$migrate_id = $this->getRandomMigrateId();
		if( is_null( $migrate_id ) )
		{	// 取得エラー
			Ethna::raiseError( 'cannot get random migrate_id', E_USER_ERROR );
			$commit = false;
		}

		// 引き継ぎパスワードを取得
		$migrate_pw = $this->getRandomMigratePassword();
		if( is_null( $migrate_pw ))
		{	// 取得エラー
			Ethna::raiseError( 'cannot get random migrate_pw', E_USER_ERROR );
			$commit = false;
		}
		
		// ハッシュデータを作る
		$migrate_pw_hash = $this->hashMigratePassword( $pp_id, $migrate_pw );
		$install_pw_hash = $this->hashInstallPassword( $pp_id, $install_pw );
		
		// ut_user_baseを更新
		$param = array( $device_type, $migrate_id, $migrate_pw_hash, $install_pw_hash, $pp_id );
		$sql = "UPDATE ut_user_base SET device_type = ?, migrate_id = ?, migrate_pw_hash = ?, install_pw_hash = ?, date_modified = NOW() WHERE pp_id = ?";
		if ( $commit ) {
			if( !$this->db->execute( $sql, $param ))
			{	// エラー
				Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
				$commit = false;
			}
		}

		// 更新ができたらキャッシュをクリア
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_m->clear( "ut_user_base__$pp_id" );
		
		// ut_portal_user_baseを更新
		$param = array( $uuid, $pp_id );
		$sql = "UPDATE ut_portal_user_base SET email = '', password = '', uuid = ?, date_modified = NOW() WHERE pp_id = ?";
		if ( $commit ) {
			if ( !$this->db->execute( $sql, $param ) ) {
				Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
				$commit = false;
			}
		}
		
		// ログテーブルに旧パスワード等を保存（念のため）
		$param = array( $pp_id, $email, $this->getEncryptedPassword( $password ) );
		$sql = "INSERt INTO ut_portal_old_user( pp_id, email, password, date_created ) VALUES( ?, ?, ?, NOW() )";
		if ( $commit ) {
			if ( !$this->db->execute( $sql, $param ) ) {
				Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
					$this->db_log->db->ErrorNo(), $this->db_log->db->ErrorMsg(), __FILE__, __LINE__ );
				$commit = false;
			}
		}

		// 返す値
		$data = array(
			'pp_id'			=> $pp_id,		// サイコパスID
			'install_pw'	=> $install_pw,	// インストールパスワード
			'migrate_id'	=> $migrate_id,	// 引き継ぎID
			'migrate_pw'	=> $migrate_pw,	// 引き継ぎPW
		);

		if ( $commit )	$this->db->commit();
		else			{$this->db->rollback();return false;}
		
		return $data;
	}
	
	/**
	 * 旧ユーザー情報の取得
	 */
	function getOldUser ( $email, $password, $dsn = "db" )
	{
		$param = array( $email, $this->getEncryptedPassword( $password ) );
		$sql = "SELECT * FROM ut_portal_user_base WHERE email = ? AND password = ?";
		$row = $this->$dsn->GetRow( $sql, $param );
		
		if ( Ethna::isError( $row ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->$dsn->db->ErrorNo(), $this->$dsn->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		return $row;
	}
	
	/**
	 * ポイントの付与
	 * この処理自体ではトランザクションを行わない。この外側で行うこと
	 */
	function addPoint ( $pp_id, $point, $memo = null )
	{
		// 0ポイントはエラー扱い
		if ( $point == 0 ) {
			return Ethna::raiseError( "point add failed FILE[%s] LINE[%d]", E_USER_ERROR, 
				__FILE__, __LINE__ );
		}
		
		// pt_user_baseを更新
		$param = array( $point, $pp_id );
		$sql = "UPDATE ut_portal_user_base SET point = point + ?, date_modified = NOW() WHERE pp_id = ?";
		if ( !$this->db->execute( $sql, $param ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		// ut_portal_point_historyに記録
		$param = array( $pp_id, ( $point > 0 ? 1 : 2 ), $point, $memo );
		$sql = "INSERT INTO ut_portal_point_history( pp_id, type, point, memo, date_created ) VALUES( ?, ?, ?, ?, NOW() )";
		if ( !$this->db->execute( $sql, $param ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
				$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		// 更新ができたらキャッシュをクリア
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_m->clear( "ut_portal_user_base_$pp_id" );
		
		return true;
	}
	
    /**
	 * ユーザー基本情報の取得
	 * テーマが各ページでほぼ必須のため、pt_user_themeとjoinして持ってくる
	 *
	 * @param int $pp_id サイコパスID
	 * @param string $dsn DNS文字列
	 *
	 * @return array|object ユーザー基本情報
	 */
	function getUserBase ( $pp_id, $dsn = "db_r" )
	{
		$param = array( CURRENT_FLG_CURRENT, $pp_id );
		$sql = "SELECT ub.*, ut.theme_id FROM ut_portal_user_base ub LEFT JOIN ut_portal_user_theme ut ON ub.pp_id = ut.pp_id AND ut.current_flg = ? WHERE ub.pp_id = ?";
		return $this->$dsn->getRow( $sql, $param );
	}
	
	/**
	 * ユーザー情報の作成
	 */
	function insertUserBase ( $pp_id, $user_name, $user_name_en, $uuid, $sex )
	{
		$param = array( $pp_id, $user_name, $user_name_en, $uuid, $sex );
		$sql = "INSERT INTO ut_portal_user_base( pp_id, user_name, user_name_en, uuid, sex, date_created, date_modified ) VALUES( ?, ?, ?, ?, ?, NOW(), NOW() )";
		if ( !$this->db->execute( $sql, $param ) ) {
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR, 
						$this->db->db->ErrorNo(), $this->db->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		
		return true;
	}
	
	/**
	 * ユーザー基本情報の更新
	 *
	 * @param int $pp_id サイコパスID
	 *
	 * @return array:ユーザー基本情報 | null:取得エラー
	 */
	function updateUserBase ( $pp_id, $columns )
	{
		if ( empty( $pp_id ) || count( $columns ) == 0 )
		{	// 更新対象の指定がないor更新対象のパラメータ異常
			return false;
		}
		
		// 主キーが更新されるといかんので更新内容から削除
		if ( isset( $columns['pp_id'] ) ) unset( $columns['pp_id'] );

		// DB更新
		$str_set = array();
		$param = array();
		foreach( $columns as $k => $v )
		{
			$str_set[] = "$k = ?";
			$param[] = $v;
		}
		$param[] = $pp_id;
		$sql = "UPDATE ut_portal_user_base SET ".implode( ',', $str_set ).", date_modified = NOW() WHERE pp_id = ?";
		if ( !$this->db->execute( $sql, $param ) )
		{	// 更新エラー
			return false;
		}

		// 更新された行数をチェック
		if ( $this->db->db->affected_rows() == 0 )
		{
			return false;
		}

		// 更新ができたらキャッシュをクリア
	//	$this->_cache_clear( "ut_portal_user_base_" . $pp_id );
		$cache_m =& Ethna_CacheManager::getInstance( 'memcache' );
		$cache_m->clear( "ut_portal_user_base_$pp_id" );

		return true;
	}
	
	/**
	 * 指定文字列の暗号化（旧パスワードの検証用）
	 *
	 * @param string $password 暗号化する文字列
	 * @return string 生成した文字列
	 */
	function getEncryptedPassword ( $password )
	{
		$key = md5( "AKHGHIFODVAWWQGEPYWQ" );
		$td  = mcrypt_module_open( 'des', '', 'ecb', '' );
		$key = substr( $key, 0, mcrypt_enc_get_key_size( $td ) );
		$iv  = mcrypt_create_iv( mcrypt_enc_get_iv_size( $td ), MCRYPT_RAND );
		if ( mcrypt_generic_init( $td, $key, $iv ) < 0 ) {
			$msg = "エラーが発生しました";
		}
		$crypt_pass = base64_encode( mcrypt_generic( $td, $password ) );
		mcrypt_generic_deinit( $td );
		mcrypt_module_close( $td );

		return $crypt_pass;
	}
}
?>
