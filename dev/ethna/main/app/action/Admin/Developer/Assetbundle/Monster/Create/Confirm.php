<?php
/**
 *  Admin/Developer/Assetbundle/Monster/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_assetbundle_monster_create_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleMonsterCreateConfirm extends Pp_Form_AdminDeveloperAssetbundleMonster
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'monster_icon',
        'monster_image',
        'asset_bundle_android' => array('required' => true),
        'asset_bundle_iphone'  => array('required' => true),
        'asset_bundle_pc'      => array('required' => true),
        'start_date',
        'end_date',
		'active_flg',
    );
}

/**
 *  admin_developer_assetbundle_monster_create_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleMonsterCreateConfirm extends Pp_Action_AdminDeveloperAssetbundle
{
    /**
     *  preprocess of admin_developer_assetbundle_monster_create_confirm Action.
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
		
		// キャッシュ準備
		$confirm_uniq = uniqid();
		$cache_contents = array(
			'confirm_uniq' => $confirm_uniq,
			'file' => array(),
		);
		
		foreach (array(
			'asset_bundle_android' => 'Android',
			'asset_bundle_iphone'  => 'iPhone',
			'asset_bundle_pc'      => '',
		) as $name => $device_name) {
			$joint_file_name = $this->af->getFileName($name);
			$cache_contents[$name] = array(
				'name' => $joint_file_name,
				'contents' => $this->af->getFileContents($name),
			);
			
			// DBに登録されていないことを確認
			$splitted = Pp_AdminAssetbundleManager::splitFileName($joint_file_name);
			$dir = $assetbundle_m->getMonsterDir($splitted['file_name']);
			if ($assetbundle_m->getMasterAssetBundleByUniqueKey(
				1, $dir, $splitted['file_name'], $splitted['version']
			)) {
				$this->af->ae->add(null, "既に登録済みです。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}
		}
		
		$monster_id = $assetbundle_m->getMonsterId($splitted['file_name']);
		$version    = $splitted['version'];
		$file_name  = $splitted['file_name'];
		
		$cache_contents['monster_id'] = $monster_id;
		$cache_contents['version']    = $version;
		$cache_contents['file_name']  = $file_name;
		
		foreach (array('monster_icon', 'monster_image') as $name) {
			$filename = $this->af->getFileName($name);
			if (!$filename) {
				//OK
				continue;
			}
			
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
		$this->af->setApp('file_name',    $file_name);
    }

    /**
     *  admin_developer_assetbundle_monster_create_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_assetbundle_monster_create_confirm';
    }
}

?>