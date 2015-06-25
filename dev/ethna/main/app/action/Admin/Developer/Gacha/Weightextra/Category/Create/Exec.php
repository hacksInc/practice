<?php
/**
 *  Admin/Developer/Gacha/Weightextra/Category/Create/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../../Pp_Form_AdminDeveloperGacha.php';

/**
 *  admin_developer_gacha_weightextra_category_create_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaWeightextraCategoryCreateExec extends Pp_Form_AdminDeveloperGacha
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'gacha_id',
		'rarity' => array('custom' => 'checkGachaExtraCatgoryRarityNotExists'),
		'weight_float',
    );
}

/**
 *  admin_developer_gacha_weightextra_category_create_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaWeightextraCategoryCreateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_weightextra_category_create_exec Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
//			return $ret;
			return 'admin_developer_gacha_weightextra_category_create_input';
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }

    /**
     *  admin_developer_gacha_weightextra_category_create_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$shop_m =& $this->backend->getManager('AdminShop');
		$admin_m =& $this->backend->getManager('Admin');
		
		$gacha_id     = $this->af->get('gacha_id');
		$rarity       = $this->af->get('rarity');
		$weight_float = $this->af->get('weight_float');

		$weight = $shop_m->convertWeightFloatToWeight($weight_float);
		
		$columns = array(
			'gacha_id' => $gacha_id,
			'rarity'   => $rarity,
			'weight'   => $weight,
		);
		
		// トランザクション開始
//		$db =& $this->backend->getDB();
//		$db->begin();

		// DBへ登録
		$ret = $shop_m->insertGachaExtraCategory($columns);
		if (!$ret || Ethna::isError($ret)) {
//			$db->rollback();
			return 'admin_error_500';
		}
		
		// トランザクション完了
//		$db->commit();
		
		// ログ
		$log_columns = $columns;
		$log_columns['weight_float'] = $weight_float;
		$log_columns['user'] = $this->session->get('lid');
		$log_columns['action'] = $this->backend->ctl->getCurrentActionName();

		$admin_m->addAdminOperationLog('/developer/gacha', 'category_log', $log_columns);
		
        return 'admin_developer_gacha_weightextra_category_create_exec';
    }
}

?>