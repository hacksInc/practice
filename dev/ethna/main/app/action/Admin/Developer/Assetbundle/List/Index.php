<?php
/**
 *  Admin/Developer/Assetbundle/List/Index.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_developer_assetbundle_list_index Form implementation.
 *
 *  @author		{$author}
 *  @access		public
 *  @package	Pp
 */
class Pp_Form_AdminDeveloperAssetbundleListIndex extends Pp_Form_AdminDeveloperAssetbundleListBase
{
	var $form = array(
		'search_file_name',
		'search_flg',
		'start',
	);
}

/**
 *  admin_developer_assetbundle_list_index action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminDeveloperAssetbundleListIndex extends Pp_Action_AdminDeveloperAssetbundleListBase
{

	public function prepare()
	{
		if ($this->af->validate() > 0)
		{
			return 'admin_developer_assetbundle_list_index';
		}

		return null;
	}

	/**
	 *  admin_developer_assetbundle_list_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	public function perform()
	{
		$file_name = $this->af->get('search_file_name');

		$search_params = array(
			'file_name' => $file_name,
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		if ($search_flg != '1')
		{
			return 'admin_developer_assetbundle_list_index';
		}

		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		$list = $assetbundle_m->getLatestAssetBundleList($search_params);

		$this->af->setApp('list', $list);

		return 'admin_developer_assetbundle_list_index';
	}
}
