<?php
/**
 *  Admin/Developer/Gacha/Weightextra/Category/Delete/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../../Pp_Form_AdminDeveloperGacha.php';

/**
 *  admin_developer_gacha_weightextra_category_delete_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaWeightextraCategoryDeleteExec extends Pp_Form_AdminDeveloperGacha
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'gacha_id',
		'rarity',
    );
}

/**
 *  admin_developer_gacha_weightextra_category_delete_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaWeightextraCategoryDeleteExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_weightextra_category_delete_exec Action.
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
     *  admin_developer_gacha_weightextra_category_delete_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$shop_m  =& $this->backend->getManager('AdminShop');
		$admin_m =& $this->backend->getManager('Admin');
		
		$gacha_id = $this->af->get('gacha_id');
		$rarity   = $this->af->get('rarity');
		
		// トランザクション開始
//		$db =& $this->backend->getDB();
//		$db->begin();

		// DBへ登録
		$ret = $shop_m->deleteGachaExtraCategory($gacha_id, $rarity);
		if (!$ret || Ethna::isError($ret)) {
//			$db->rollback();
			return 'admin_error_500';
		}
		
		// トランザクション完了
//		$db->commit();
		
		// ログ
		$admin_m->addAdminOperationLog('/developer/gacha', 'category_log', array(
			'gacha_id' => $gacha_id,
			'rarity'   => $rarity,
			'user'     => $this->session->get('lid'),
			'action'   => $this->backend->ctl->getCurrentActionName(),
		));
		
        return 'admin_developer_gacha_weightextra_category_delete_exec';
    }
}

?>