<?php
/**
 *  Admin/Developer/Assetbundle/Sound/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/** admin_developer_assetbundle_sound_* で共通のアクションフォーム定義 */
class Pp_Form_AdminDeveloperAssetbundleSound extends Pp_Form_AdminDeveloperAssetbundle
{
	/**
	 * アセットバンドルのファイルをチェックする
	 */
	function checkAssetbundleFile($name)
	{
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');

		$joint_file_name = $this->getFileName($name);
		if (strlen($joint_file_name) == 0) {
			$def = $this->getDef($name);
			if (!$def['required']) {
				//OK
				return;
			}
		}
		
		$splitted = Pp_AdminAssetbundleManager::splitFileName($joint_file_name);

		// 先頭を確認
		if (!preg_match('/^[A-Za-z0-9_\.]+$/', $joint_file_name) ||
			(strpos($joint_file_name, '..') !== false)
		) {
			$this->ae->add($name, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
			return;
		}
			
		// ヴァージョンを確認
		$version_tmp = $splitted['version'];
		if (!preg_match('/^[0-9]+$/', $version_tmp)) {
			$this->ae->add($name, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
			return;
		}
		
		// device_nameを確認
		$device_name_def = array(
			'asset_bundle_android' => 'Android',
			'asset_bundle_iphone'  => 'iPhone',
			'asset_bundle_pc'      => '',
		);
		$device_name = $device_name_def[$name];
		if ($device_name != $splitted['device_name']) {
			$this->ae->add($name, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
			return;
		}
			
		// 末尾を確認
		if (!preg_match('/\.unity3d$/', $joint_file_name)) {
			$this->af->ae->add($name, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
			return;
		}
			
		// バイナリが空でないことを確認
		$contents = $this->getFileContents($name);
		if (strlen($contents) == 0) {
			$this->ae->add($name, $name . "の内容が空です。", E_ERROR_DEFAULT);
			return;
		}
	}
}

/**
 *  admin_developer_assetbundle_sound_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleSoundIndex extends Pp_Form_AdminDeveloperAssetbundleSound
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'pageID',
    );
}

/**
 *  admin_developer_assetbundle_sound_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleSoundIndex extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_assetbundle_sound_index Action.
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
     *  admin_developer_assetbundle_sound_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_assetbundle_sound_index';
    }
}

?>