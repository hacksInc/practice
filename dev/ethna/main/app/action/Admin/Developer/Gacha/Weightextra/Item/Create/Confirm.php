<?php
/**
 *  Admin/Developer/Gacha/Weightextra/Item/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../../Pp_Form_AdminDeveloperGacha.php';

/**
 *  admin_developer_gacha_weightextra_item_create_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaWeightextraItemCreateConfirm extends Pp_Form_AdminDeveloperGacha
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'gacha_id',
		'monster_id' => array('custom' => 'checkGachaExtraItemMonsterIdCreatable'),
		'monster_lv' => array('custom' => 'checkGachaMonsterMaxLv' ),
		'weight_float',
    );
}

/**
 *  admin_developer_gacha_weightextra_item_create_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaWeightextraItemCreateConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_weightextra_item_create_confirm Action.
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
			if ($ret == 'admin_error_400') {
				return 'admin_developer_gacha_weightextra_item_create_input';
			} else {
				return $ret;
			}
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }

    /**
     *  admin_developer_gacha_weightextra_item_create_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_gacha_weightextra_item_create_confirm';
    }
}

?>