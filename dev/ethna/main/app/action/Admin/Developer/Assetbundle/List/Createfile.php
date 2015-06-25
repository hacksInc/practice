<?php
/**
 *  Admin/Developer/Assetbundle/List/Createfile.php
 *
 *  @author	 	$author}
 *  @access	 	public
 *  @package	Pp
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_developer_assetbundle_list_createfile Form implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Form_AdminDeveloperAssetbundleListCreatefile extends Pp_Form_AdminDeveloperAssetbundleListBase
{
	var $form = array(
		'search_file_name' => array(
			'filter' => 'urldecode',
		),
		'search_flg',
		'start',
		'type',	// d:daily / m:monthly
	);
}

/**
 *  admin_developer_assetbundle_list_createfile action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminDeveloperAssetbundleListCreatefile extends Pp_Action_AdminDeveloperAssetbundleListBase
{
	function prepare()
	{
		if ($this->af->validate() > 0)
		{
			return 'admin_json_encrypt';
		}
		return null;
	}

	/**
	 *  admin_developer_assetbundle_list_createfile action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		if ($this->af->get('search_flg') != '1')
		{
			$this->af->setApp('code', 100);
			return 'admin_json_encrypt';
		}

		$file_name = $this->af->get('search_file_name');

		$search_params = array(
			'file_name' => $file_name,
		);

		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');

		$count = $assetbundle_m->getLatestAssetBundleCount($search_params);

		if ($count === 0)
		{
			$this->af->setApp('code', 400);
			$this->af->setApp('err_msg', "対象となるデータが存在しません。");
			return 'admin_json_encrypt';
		}

		$list = $assetbundle_m->getLatestAssetBundleList($search_params);

		$file_name = $assetbundle_m->createLatestAssetBundleCsv($list);

		if ($file_name === false)
		{
			$this->af->setApp('code', 400);
			$this->af->setApp('err_msg', 'ファイルの作成に失敗しました。');
			return 'admin_json_encrypt';
		}

		$this->af->setApp('code', 200);
		$this->af->setApp('file_name', $file_name);

		return 'admin_json_encrypt';
	}
}
