<?php
/**
 *  Admin/Developer/Assetbundle/Effect/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/** admin_developer_assetbundle_effect_* で共通のアクションフォーム定義 */
class Pp_Form_AdminDeveloperAssetbundleEffect extends Pp_Form_AdminDeveloperAssetbundle
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

		// ファイル名を確認
		if (strcmp($splitted['file_name'], 'effect') !== 0) {
			$this->ae->add($name, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
			return;
		}
			
		// ヴァージョンを確認
		if (!preg_match('/^[0-9]+$/', $splitted['version'])) {
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
 *  admin_developer_assetbundle_effect_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleEffectIndex extends Pp_Form_AdminDeveloperAssetbundleEffect
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
 *  admin_developer_assetbundle_effect_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleEffectIndex extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_assetbundle_effect_index Action.
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
     *  admin_developer_assetbundle_effect_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_assetbundle_effect_index';
    }
}

?>