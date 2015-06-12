<?php
/**
 *  Admin/Developer/Gacha/Banner/Clear/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_gacha_banner_clear_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaBannerClearExec extends Pp_Form_AdminDeveloperGacha
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'gacha_id',
    );
}

/**
 *  admin_developer_gacha_banner_clear_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaBannerClearExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_banner_clear_exec Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
/*
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }
*/

    /**
     *  admin_developer_gacha_banner_clear_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$shop_m =& $this->backend->getManager( 'AdminShop' );
		$admin_m =& $this->backend->getManager( 'Admin' );

		$gacha_id = $this->af->get( 'gacha_id' );

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		// ガチャリスト管理情報を削除
		$ret = $shop_m->deleteGachaListInfo( $gacha_id );
		if( !$ret || Ethna::isError( $ret ))
		{
			$db->rollback();
			return 'admin_error_500';
		}
		// 一時抽選情報を削除
		$ret = $shop_m->deleteGachaCategoryInfo( $gacha_id );
		if( !$ret || Ethna::isError( $ret ))
		{
			$db->rollback();
			return 'admin_error_500';
		}
		// 二次抽選情報を削除
		$ret = $shop_m->deleteGachaItemInfo( $gacha_id );
		if( !$ret || Ethna::isError( $ret ))
		{
			$db->rollback();
			return 'admin_error_500';
		}

		// おまけガチャリスト管理情報を削除
		$ret = $shop_m->deleteGachaExtraListInfo( $gacha_id );
		if( !$ret || Ethna::isError( $ret ))
		{
			$db->rollback();
			return 'admin_error_500';
		}
		// おまけガチャ一時抽選情報を削除
		$ret = $shop_m->deleteGachaExtraCategoryInfo( $gacha_id );
		if( !$ret || Ethna::isError( $ret ))
		{
			$db->rollback();
			return 'admin_error_500';
		}
		// おまけガチャ二次抽選情報を削除
		$ret = $shop_m->deleteGachaExtraItemInfo( $gacha_id );
		if( !$ret || Ethna::isError( $ret ))
		{
			$db->rollback();
			return 'admin_error_500';
		}

		// トランザクション終了
		$db->commit();

		$this->af->setApp('gacha_id', $gacha_id);

		// ログ
		$admin_m->addAdminOperationLog( '/developer/gacha', 'banner_log', array(
			'gacha_id' => $gacha_id,
			'user'     => $this->session->get('lid'),
			'action'   => $this->backend->ctl->getCurrentActionName(),
		));
		
        return 'admin_developer_gacha_banner_clear_exec';
    }
}

?>