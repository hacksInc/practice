<?php
/**
 *  Admin/Developer/Assetbundle/Map/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_assetbundle_map_update_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleMapUpdateConfirm extends Pp_Form_AdminDeveloperAssetbundleMap
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'id',
        'asset_bundle_android' => array('required' => false),
        'asset_bundle_iphone'  => array('required' => false),
        'asset_bundle_pc'      => array('required' => false),
        'start_date',
        'end_date',
		'active_flg',
    );
}

/**
 *  admin_developer_assetbundle_map_update_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleMapUpdateConfirm extends Pp_Action_AdminDeveloperAssetbundle
{
    /**
     *  preprocess of admin_developer_assetbundle_map_update_confirm Action.
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
		
		$id = $this->af->get('id');
		$row = $assetbundle_m->getMasterAssetBundle($id);
		if (!is_array($row)) {
			$this->af->ae->add(null, "修正対象が存在しません。", E_ERROR_DEFAULT);
			return 'admin_error_400';
		}
		
		// キャッシュ準備
		$confirm_uniq = uniqid();
		$cache_contents = array(
			'confirm_uniq' => $confirm_uniq,
			'file' => array(),
		);
		
		// アップロードされたファイル名を検証＆キャッシュ準備
		$map_id = null;
		$version = null;
		foreach (array(
			'asset_bundle_android' => 'Android',
			'asset_bundle_iphone'  => 'iPhone',
			'asset_bundle_pc'      => '',
		) as $name => $device_name) {
			$joint_file_name = $this->af->getFileName($name);
			if (strlen($joint_file_name) == 0) {
				//OK
				continue;
			}
			
			$splitted = Pp_AdminAssetbundleManager::splitFileName($joint_file_name);

			// IDを確認
			$map_id_tmp = $assetbundle_m->getBgmodelId($splitted['file_name']);
			if (!$map_id) {
				$map_id = $map_id_tmp;
			} else if ($map_id != $map_id_tmp) {
				error_log('DEBUG:' . __FILE__ . ':' . __LINE__);
				$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}
			
			// ヴァージョンを確認
			$version_tmp = $splitted['version'];

			if (!$version) {
				$version = $version_tmp;
				if ($version != $row['version']) {
					$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
					return 'admin_error_400';
				}
			} else if ($version != $version_tmp) {
				error_log('DEBUG:' . __FILE__ . ':' . __LINE__);
				$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}
			
			// キャッシュ準備
			$contents = $this->af->getFileContents($name);
			$cache_contents[$name] = array(
				'name' => $joint_file_name,
				'contents' => $contents,
			);
		}
		
		if (!$version) {
			$version = $row['version'];
		}
		
		$cache_contents['id']         = $id;
		$cache_contents['version']    = $version;
		$cache_contents['file_name']  = $row['file_name'];
		
		foreach (array('start_date', 'end_date', 'active_flg') as $name) {
			$cache_contents[$name] = $this->af->get($name);
		}
		
		// キャッシュする
		$this->setCacheContents($this->session->get('lid'), $cache_contents);

		// テンプレート変数アサイン
		$this->af->setApp('confirm_uniq', $confirm_uniq);
		$this->af->setApp('version',      $version);
		$this->af->setApp('file_name',    $row['file_name']);
		$this->af->setApp('dir',          $row['dir']);
    }

    /**
     *  admin_developer_assetbundle_map_update_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_assetbundle_map_update_confirm';
    }
}

?>