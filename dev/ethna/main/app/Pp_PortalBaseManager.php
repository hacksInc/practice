<?php
/**
 *  Pp_PortalBaseManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

define( "DSN_TYPE_RO", 1 );
define( "DSN_TYPE_RW", 2 );

/**
 *  Pp_PortalBaseManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_PortalBaseManager extends Ethna_AppManager
{
	/**
	 * password の暗号化
	 * 旧ポータルで使われていたもの。ポータル関係でしか使わない気がするのでここに設置
	 * 
	 * return	string		生成した password
	 */
	function getEncryptedPassword ( $password )
	{
		$key = md5( PORTAL_ENC_KEY );
		$td  = mcrypt_module_open('des', '', 'ecb', '');
		$key = substr($key, 0, mcrypt_enc_get_key_size($td));
		$iv  = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
		if (mcrypt_generic_init($td, $key, $iv) < 0) {
			$msg = "エラーが発生しました";
		}
		$crypt_pass = base64_encode(mcrypt_generic($td, $password));
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return $crypt_pass;
	}
}
?>
