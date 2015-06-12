<?php
/**
 *  Admin/Developer/Gacha/Weightextra/Category/Update/Multi/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../../../Pp_Form_AdminDeveloperGacha.php';

/**
 *  admin_developer_gacha_weightextra_category_update_multi_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaWeightextraCategoryUpdateMultiExec extends Pp_Form_AdminDeveloperGacha
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'gacha_id',
		'rarities',
		'weights_float',
    );
}

/**
 *  admin_developer_gacha_weightextra_category_update_multi_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaWeightextraCategoryUpdateMultiExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_weightextra_category_update_multi_exec Action.
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
     *  admin_developer_gacha_weightextra_category_update_multi_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$shop_m =& $this->backend->getManager('AdminShop');
		$admin_m =& $this->backend->getManager('Admin');
		
		$gacha_id = $this->af->get('gacha_id');
		$rarities = $this->af->get('rarities');
		
		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		// DBへ登録
		$columns_stack = array();
		foreach ($rarities as $rarity) {
			$weight_float = $this->af->getWeightFloatByRarity($rarity);
			$weight = $shop_m->convertWeightFloatToWeight($weight_float);

			$columns = array(
				'gacha_id' => $gacha_id,
				'rarity'   => $rarity,
				'weight'   => $weight,
			);
			
			$ret = $shop_m->updateGachaExtraCategory($columns);
			if (!$ret || Ethna::isError($ret)) {
				$db->rollback();
				return 'admin_error_500';
			}
			
			$columns['weight_float'] = $weight_float;
			$columns_stack[] = $columns;
		}
		
		// トランザクション完了
		$db->commit();
		
		// ログ
		foreach ($columns_stack as $columns) {
			$columns['user'] = $this->session->get('lid');
			$columns['action'] = $this->backend->ctl->getCurrentActionName();

			$admin_m->addAdminOperationLog('/developer/gacha', 'category_log', $columns);
		}
		
        return 'admin_developer_gacha_weightextra_category_update_multi_exec';
    }
}

?>