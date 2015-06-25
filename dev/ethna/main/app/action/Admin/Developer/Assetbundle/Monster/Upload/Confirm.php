<?php
/**
 *  Admin/Developer/Assetbundle/Monster/Upload/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_assetbundle_monster_upload_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleMonsterUploadConfirm extends Pp_Form_AdminDeveloperAssetbundleMonster
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'monster_icon',
        'monster_image',
        'asset_bundle_android',
        'asset_bundle_iphone',
        'asset_bundle_pc',
        'start_date',
        'end_date',
        'active_flg',
    );
}

/**
 *  admin_developer_assetbundle_monster_upload_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleMonsterUploadConfirm extends Pp_Action_AdminDeveloperAssetbundle
{
    /**
     *  preprocess of admin_developer_assetbundle_monster_upload_confirm Action.
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

		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		
		// キャッシュ準備
		$confirm_uniq = uniqid();
		$cache_contents = array(
			'confirm_uniq' => $confirm_uniq,
			'file' => array(),
		);
		
		// アップロードされたファイル名を検証＆キャッシュ準備
		$monster_id = null;
		$version = null;
		foreach (array(
			'asset_bundle_android' => 'Android',
			'asset_bundle_iphone'  => 'iPhone',
			'asset_bundle_pc'      => '',
		) as $name => $device_name) {
			$joint_file_name = $this->af->getFileName($name);
			$splitted = Pp_AdminAssetbundleManager::splitFileName($joint_file_name);

			// 先頭を確認
			if (!preg_match('/^monster_atlas_/', $joint_file_name)) {
				error_log('DEBUG:' . __FILE__ . ':' . __LINE__);
				$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}
			
			// モンスターIDを確認
			$monster_id_tmp = substr($splitted['file_name'], strlen('monster_atlas_'));
			if (!preg_match('/^[0-9]+$/', $monster_id_tmp)) {
				error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . $splitted['file_name'] . ':' . $monster_id_tmp);
				$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}

			if (!$monster_id) {
				$monster_id = $monster_id_tmp;
			} else if ($monster_id != $monster_id_tmp) {
				error_log('DEBUG:' . __FILE__ . ':' . __LINE__);
				$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}
			
			// ヴァージョンを確認
			$version_tmp = $splitted['version'];
			if (!preg_match('/^[0-9]$/', $version_tmp)) {
				error_log('DEBUG:' . __FILE__ . ':' . __LINE__);
				$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}

			if (!$version) {
				$version = $version_tmp;
			} else if ($version != $version_tmp) {
				error_log('DEBUG:' . __FILE__ . ':' . __LINE__);
				$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}
			
			// device_nameを確認
			if ($device_name != $splitted['device_name']) {
				error_log('DEBUG:' . __FILE__ . ':' . __LINE__);
				$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}
			
			// 末尾を確認
			if (!preg_match('/\.unity3d$/', $joint_file_name)) {
				error_log('DEBUG:' . __FILE__ . ':' . __LINE__);
				$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}
			
			// バイナリが空でないことを確認
			$contents = $this->af->getFileContents($name);
			if (strlen($contents) == 0) {
				$this->af->ae->add(null, $name . "の内容が空です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}

			$cache_contents[$name] = array(
				'name' => $joint_file_name,
				'contents' => $contents,
			);
		}
		
		$cache_contents['monster_id'] = $monster_id;
		$cache_contents['version']    = $version;
		$cache_contents['file_name']  = $splitted['file_name'];
		
		foreach (array('monster_icon', 'monster_image') as $name) {
			$filename = $this->af->getFileName($name);
			
			// 拡張子を確認
			if (!preg_match('/^' . $name . '_' . $monster_id . '\.png$/', $filename)) {
				$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}
			
			// バイナリが空でないことを確認
			$contents = $this->af->getFileContents($name);
			if (strlen($contents) == 0) {
				$this->af->ae->add(null, $name . "の内容が空です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}

			$cache_contents[$name] = array(
				'name' => $filename,
				'contents' => $contents,
			);
		}

		foreach (array('start_date', 'end_date', 'active_flg') as $name) {
			$cache_contents[$name] = $this->af->get($name);
		}
		
		// キャッシュする
		$this->setCacheContents($this->session->get('lid'), $cache_contents);

		// テンプレート変数アサイン
		$this->af->setApp('confirm_uniq', $confirm_uniq);
		$this->af->setApp('monster_id',   $monster_id);
		$this->af->setApp('version',      $version);
		$this->af->setApp('file_name',    $splitted['file_name']);
*/
    }

    /**
     *  admin_developer_assetbundle_monster_upload_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
return 'admin_error_500';
//      return 'admin_developer_assetbundle_monster_upload_confirm';
    }
}

?>