<?php
/**
 *  Admin/Kpi/Gacha/User/Index.php
 *
 *  @author	 	{$author}
 *  @package	Pp
 *  @version	$Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Base.php';

/**
 *  admin_kpi_gacha_user_index Form implementation.
 *
 *  @author		{$author}
 *  @access		public
 *  @package	Pp
 */
class Pp_Form_AdminKpiGachaUserIndex extends Pp_Form_AdminKpiGachaUserBase
{
	var $form = array(
		'search_date_from',
		'search_date_to',
		'search_ua',
		'search_gacha_ids',
		'search_flg',
		'start',
	);
	
	/**
	 *  Form elements define change
	 *
	 *  @access public
	 *  @return void
	 */
	function setFormDef_PreHelper()
	{
		$photo_gacha_m = $this->backend->getManager('PhotoGacha');
		$gacha_list = $photo_gacha_m->getMasterPhotoGachaAll();

		$def = $this->form['search_gacha_ids'];
		foreach ($gacha_list as $v)
		{
			$def['option'][$v['gacha_id']] = $v['gacha_id'].':'.$v['name_ja'];
		}
		$this->setDef('search_gacha_ids', $def);
	}
}

/**
 *  admin_kpi_gacha_user_index action implementation.
 *
 *  @author	 	{$author}
 *  @access	 	public
 *  @package	Pp
 */
class Pp_Action_AdminKpiGachaUserIndex extends Pp_Action_AdminKpiGachaUserBase
{

	public function prepare()
	{
		$search_flg = $this->af->get('search_flg');
		if ($search_flg == '1')
		{
			if ($this->af->validate() > 0)
			{
				return 'admin_kpi_gacha_user_index';
			}
		}
		return null;
	}

	/**
	 *  admin_kpi_gacha_user_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	public function perform()
	{
		$date_from = $this->af->get('search_date_from');
		$date_to = $this->af->get('search_date_to');

		$search_params = array(
			'date_from' => $date_from,
			'date_to' => $date_to,
			'ua' => $this->af->get('search_ua'),
			'gacha_ids' => $this->af->get('search_gacha_ids'),
		);

		$search_flg = $this->af->get('search_flg');
		$this->af->setApp('search_flg', $search_flg);
		if ($search_flg != '1')
		{
			return 'admin_kpi_gacha_user_index';
		}

		$kpigacha_m = $this->backend->getManager('KpiViewGacha');
		$list = $kpigacha_m->getListInGachaUser($search_params);
		$view_list = array();
		foreach ($list as $key => $v)
		{
			$view_list[$v['gacha_id']]['data'][$key] = $v;
			$view_list[$v['gacha_id']]['gacha_name'] = $v['name'];
		}

		$this->af->setApp('view_list', $view_list);

		return 'admin_kpi_gacha_user_index';
	}
}
