<?php
/**
 *	Api/Client/Ver.php
 *	バージョン情報取得
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  api_client_ver Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiClientVer extends Pp_ApiActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  api_client_ver action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiClientVer extends Pp_ApiActionClass
{
    /**
     *  preprocess of api_client_ver Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        if ($this->af->validate() > 0) {
            return 'error_400';
        }

        return null;
    }

    /**
     *  api_client_ver action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		
		$client_m =& $this->backend->getManager( "Client" );

		$app_ver = $client_m->getLatestAppVer( $date );
		$res_ver = $client_m->getLatestResVer( $date );

		// ヘッダに含まれているapp_ver、res_verを取得して比較
		$headers = getAllHeaders();
		
		// アプリバージョンが古かったらエラー
		if ( isset( $headers['x-psycho-appver'] ) ) {
			if ( $headers['x-psycho-appver'] < $app_ver['app_ver'] ) {
//				$this->af->setApp( 'status_detail_code', SDC_APPVER_NOT_LATEST, true );
//				return 'error_400';
			}
		}
		
		// リソースバージョンが古かったらエラー
		if ( isset( $headers['x-psycho-rscver'] ) ) {
			if ( $headers['x-psycho-rscver'] < $res_ver['res_ver'] ) {
//				$this->af->setApp( 'status_detail_code', SDC_RSCVER_NOT_LATEST, true );
//				return 'error_400';
			}
		}

		$this->af->setApp('app_ver', $app_ver, true);
		$this->af->setApp('res_ver', $res_ver, true);

        return 'api_json_encrypt';
    }
}

?>