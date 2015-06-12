<?php
/**
 *  Admin/Developer/Assetbundle/Bgmodel/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_assetbundle_bgmodel_create_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleBgmodelCreateConfirm extends Pp_Form_AdminDeveloperAssetbundleBgmodel
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'asset_bundle_android' => array('required' => true),
        'asset_bundle_iphone'  => array('required' => true),
        'asset_bundle_pc'      => array('required' => true),
        'start_date',
        'end_date',
		'active_flg',
    );
}

/**
 *  admin_developer_assetbundle_bgmodel_create_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleBgmodelCreateConfirm extends Pp_Action_AdminDeveloperAssetbundle
{
    /**
     *  preprocess of admin_developer_assetbundle_bgmodel_create_confirm Action.
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
		
		// アップロードされたファイル名を検証＆キャッシュ準備
		$bgmodel_id = null;
		$version = null;
		foreach (array(
			'asset_bundle_android' => 'Android',
			'asset_bundle_iphone'  => 'iPhone',
			'asset_bundle_pc'      => '',
		) as $name => $device_name) {
			$joint_file_name = $this->af->getFileName($name);
			$splitted = Pp_AdminAssetbundleManager::splitFileName($joint_file_name);

			// IDを確認
			$bgmodel_id_tmp = $assetbundle_m->getBgmodelId($splitted['file_name']);
			if (!$bgmodel_id) {
				$bgmodel_id = $bgmodel_id_tmp;
			} else if ($bgmodel_id != $bgmodel_id_tmp) {
				error_log('DEBUG:' . __FILE__ . ':' . __LINE__);
				$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}
			
			// ヴァージョンを確認
			$version_tmp = $splitted['version'];
			if (!$version) {
				$version = $version_tmp;
			} else if ($version != $version_tmp) {
				error_log('DEBUG:' . __FILE__ . ':' . __LINE__);
				$this->af->ae->add(null, $name . "のファイル名が不正です。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}
			
			// DBに登録されていないことを確認
			$dir = $assetbundle_m->getBgmodelDir($splitted['file_name']);
			if ($assetbundle_m->getMasterAssetBundleByUniqueKey(
				1, $dir, $splitted['file_name'], $version
			)) {
				$this->af->ae->add(null, "既に登録済みです。", E_ERROR_DEFAULT);
				return 'admin_error_400';
			}

			// キャッシュ準備
			$contents = $this->af->getFileContents($name);
			$cache_contents[$name] = array(
				'name' => $joint_file_name,
				'contents' => $contents,
			);
		}
		
		$cache_contents['version']    = $version;
		$cache_contents['file_name']  = $splitted['file_name'];
		
		foreach (array('start_date', 'end_date', 'active_flg') as $name) {
			$cache_contents[$name] = $this->af->get($name);
		}
		
		// キャッシュする
		$this->setCacheContents($this->session->get('lid'), $cache_contents);

		// テンプレート変数アサイン
		$this->af->setApp('confirm_uniq', $confirm_uniq);
		$this->af->setApp('version',      $version);
		$this->af->setApp('file_name',    $splitted['file_name']);
    }

    /**
     *  admin_developer_assetbundle_bgmodel_create_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_assetbundle_bgmodel_create_confirm';
    }
}

?>