<?php
/**
 *  Admin/Developer/Gacha/Banner/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_gacha_banner_update_input Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaBannerUpdateInput extends Pp_Form_AdminDeveloperGacha
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
 *  admin_developer_gacha_banner_update_input action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaBannerUpdateInput extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_banner_update_input Action.
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
     *  admin_developer_gacha_banner_update_input action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$shop_m =& $this->backend->getManager('AdminShop');
		$gacha_id = $this->af->get('gacha_id');
		
		if ($gacha_id) {
			$row = $shop_m->getGachaListId($gacha_id);
			if (!$row) {
				return 'admin_error_500';
			}
			
			$this->af->setApp('row', $row);
		}

        return 'admin_developer_gacha_banner_update_input';
    }
}

?>