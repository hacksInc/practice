<?php
/**
 *  Admin/Developer/Assetbundle/Monster/Upload/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_assetbundle_monster_upload_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleMonsterUploadExec extends Pp_Form_AdminDeveloperAssetbundleMonster
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'confirm_uniq' /* => array(
            // Form definition
            'type'        => VAR_TYPE_STRING,  // Input type
            'form_type'   => FORM_TYPE_HIDDEN, // Form type
			'name'        => 'confirm_uniq',   // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 1,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ) */,
    );
}

/**
 *  admin_developer_assetbundle_monster_upload_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleMonsterUploadExec extends Pp_Action_AdminDeveloperAssetbundle
{
    /**
     *  preprocess of admin_developer_assetbundle_monster_upload_exec Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
return 'admin_error_500';
/*
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
*/
	}

    /**
     *  admin_developer_assetbundle_monster_upload_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
return 'admin_error_500';
/*
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		$admin_m =& $this->backend->getManager('Admin');

		$cache_contents = $this->cache_contents;
		$monster_id = $cache_contents['monster_id'];
		$dir = 'monster/' . $monster_id;
		
		// DBに登録
		$columns = array(
			'file_type'  => 1,
			'dir'        => $dir,
			'file_name'  => $cache_contents['file_name'],
			'version'    => $cache_contents['version'],
			'start_date' => $cache_contents['start_date'],
			'end_date'   => $cache_contents['end_date'],
			'active_flg' => $cache_contents['active_flg'],
		);

		if ($row = $assetbundle_m->getMasterAssetBundleByUniqueKey(
			1, $dir, $cache_contents['file_name'], $cache_contents['version']
		)) {
			if ($assetbundle_m->setMasterAssetBundle($row['id'], $columns) !== true) {
				return 'admin_error_500';
			}
		} else {
			if ($assetbundle_m->createMasterAssetBundle($columns) !== true) {
				return 'admin_error_500';
			}
		}
		
		// アセットバンドルのファイルを登録
		foreach (array(
			'asset_bundle_android', 
			'asset_bundle_iphone', 
			'asset_bundle_pc'
		) as $name) {
			if (!$assetbundle_m->putAssetbundleFileContents(
				$dir, $cache_contents[$name]['name'], $cache_contents[$name]['contents']
			)) {
				return 'admin_error_500';
			}
		}
        
		// 管理画面でのみ使用する画像ファイルを登録
		$image_dir = BASE . '/data/resource/image/' . $dir;
		is_dir($image_dir) || mkdir($image_dir);
		
		foreach(array(
			'monster_icon'  => 'icon',
			'monster_image' => 'image',
		) as $name => $type) {
			$full_path = $assetbundle_m->getMonsterImagePath($type, $monster_id);
			if (!file_put_contents($full_path, $cache_contents[$name]['contents'])) {
				return 'admin_error_500';
			}
		}
		
		// ログ
		$admin_m->addAdminOperationLog('/developer/assetbundle', 'monster_log', array(
			'user'       => $this->session->get('lid'),
			'action'     => $this->backend->ctl->getCurrentActionName(),
			'file_type'  => $columns['file_type'],
			'dir'        => $columns['dir'],
			'file_name'  => $columns['file_name'],
			'version'    => $columns['version'],
			'start_date' => $columns['start_date'],
			'end_date'   => $columns['end_date'],
			'active_flg' => $columns['active_flg'],
		));
		
        return 'admin_developer_assetbundle_monster_upload_exec';
*/
    }
}

?>