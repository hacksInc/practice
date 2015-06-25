<?php
/**
 *  Admin/Developer/Assetbundle/Version/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_assetbundle_version_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleVersionUpdateExec extends Pp_Form_AdminDeveloperAssetbundleVersion
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
 *  admin_developer_assetbundle_version_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleVersionUpdateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_assetbundle_version_update_exec Action.
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
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		$date_start = $this->af->get('date_start');
		
		$row = $assetbundle_m->getVersion($date_start);
		if (!$row || Ethna::isError($row)) {
			return 'admin_error_500';
		}
    }

    /**
     *  admin_developer_assetbundle_version_update_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		$admin_m =& $this->backend->getManager('Admin');
		
		$columns = array();
		foreach ($this->af->form as $key => $value) {
			$columns[$key] = $this->af->get($key);
		}
		
		$ret = $assetbundle_m->updateVersion($columns);
		if (!$ret || Ethna::isError($ret)) {
			return 'admin_error_500';
		}
		
		// ログ
		$admin_m->addAdminOperationLog('/developer/assetbundle', 'version_log', 
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $columns)
		);

		return 'admin_developer_assetbundle_version_update_exec';
    }
}

?>