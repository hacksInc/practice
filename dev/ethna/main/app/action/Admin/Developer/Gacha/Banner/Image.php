<?php
/**
 *  Admin/Developer/Gacha/Banner/Image.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once 'Index.php';

/**
 *  admin_developer_gacha_banner_image Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaBannerImage extends Pp_AdminActionForm
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
 *  admin_developer_gacha_banner_image action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaBannerImage extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_banner_image Action.
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
     *  admin_developer_gacha_banner_image action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'resource_gachabanner';
    }
}

?>