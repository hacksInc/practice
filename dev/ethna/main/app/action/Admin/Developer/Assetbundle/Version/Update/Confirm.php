<?php
/**
 *  Admin/Developer/Assetbundle/Version/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_assetbundle_version_update_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleVersionUpdateConfirm extends Pp_Form_AdminDeveloperAssetbundleVersion
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'app_ver',
/*
        'res_ver',
        'mon_ver',
        'mon_image_ver',
        'skilldata_ver',
        'skilleffect_ver',
        'bgmodel_ver',
        'sound_ver',
        'map_ver',
		'worldmap_ver',
        'mon_exp_ver',
        'player_rank_ver',
        'ach_ver',
        'mon_act_ver',
        'boost_ver',
        'badge_ver',
        'badge_material_ver',
        'badge_skill_ver',
*/
        'clear',
		'date_start',
    );
	
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$this->initializeResVerFormDef();
		
		parent::__construct($backend);
	}
}

/**
 *  admin_developer_assetbundle_version_update_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleVersionUpdateConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_assetbundle_version_update_confirm Action.
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
     *  admin_developer_assetbundle_version_update_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_assetbundle_version_update_confirm';
    }
}

?>