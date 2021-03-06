<?php
/**
 *  Admin/Developer/Assetbundle/Effect/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_assetbundle_effect_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleEffectUpdateExec extends Pp_Form_AdminDeveloperAssetbundleEffect
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'confirm_uniq',
    );
}

/**
 *  admin_developer_assetbundle_effect_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleEffectUpdateExec extends Pp_Action_AdminDeveloperAssetbundle
{
    /**
     *  preprocess of admin_developer_assetbundle_effect_update_exec Action.
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
		
		$ret = $this->prepareCacheContents();
		if ($ret) {
			return $ret;
		}
    }

    /**
     *  admin_developer_assetbundle_effect_update_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		$admin_m =& $this->backend->getManager('Admin');

		$cache_contents = $this->cache_contents;
		$dir = 'effect';
		
		// DBに登録
		$columns = array(
			'id'         => $cache_contents['id'],
			'start_date' => $cache_contents['start_date'],
			'end_date'   => $cache_contents['end_date'],
			'active_flg' => $cache_contents['active_flg'],
		);
		
		$row = $assetbundle_m->getMasterAssetBundle($columns['id']);
		if (!is_array($row)) {
			return 'admin_error_500';
		}

		if ($assetbundle_m->updateMasterAssetBundle($columns) !== true) {
			return 'admin_error_500';
		}
		
		$log_columns = $columns;
		
		// アセットバンドルのファイルを登録
		foreach (array(
			'asset_bundle_android', 
			'asset_bundle_iphone', 
			'asset_bundle_pc'
		) as $name) {
			if (!isset($cache_contents[$name])) {
				// OK
				continue;
			}
			
			if (!$assetbundle_m->putAssetbundleFileContents(
				$dir, $cache_contents[$name]['name'], $cache_contents[$name]['contents']
			)) {
				return 'admin_error_500';
			}
			
			$log_columns[$name] = $cache_contents[$name]['name'];
		}
        
		// ログ
		$admin_m->addAdminOperationLog('/developer/assetbundle', 'effect_log', array_merge(
			array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), 
			$log_columns
		));

		return 'admin_developer_assetbundle_effect_update_exec';
    }
}

?>