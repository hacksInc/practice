<?php
/**
 *  Admin/Kpi/Device/Info/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Index.php';

/**
 *  admin_kpi_device_info_view Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiDeviceInfoView extends Pp_Form_AdminKpiDeviceInfo
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'search_month' => array('required' => true),
		'search_ua'    => array('required' => true),
		'sort_item'    => array('required' => true),
		'format',
    );
}

/**
 *  admin_kpi_device_info_view action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminKpiDeviceInfoView extends Pp_AdminActionClass
{
 	protected $validation_error_forward_name = 'admin_kpi_device_info_index';
	
   /**
     *  preprocess of admin_kpi_device_info_view Action.
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
     *  admin_kpi_device_info_view action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$client_m =& $this->backend->getManager('AdminClient');
		
		$search_flg   = $this->af->get('search_flg');
		$search_month = $this->af->get('search_month');
		$search_ua    = $this->af->get('search_ua');
		$sort_item    = $this->af->get('sort_item');
		$format       = $this->af->get('format');
		
		$period = date('ym', strtotime($search_month . '-01 00:00:00'));
			
		switch ($sort_item) {
			case Pp_AdminClientManager::DEVICE_INFO_SORT_ITEM_DEVICE_COUNT_ASC:
			case Pp_AdminClientManager::DEVICE_INFO_SORT_ITEM_DEVICE_PERCENTAGE_ASC:
				$direction = 'asc';
				break;
				
			case Pp_AdminClientManager::DEVICE_INFO_SORT_ITEM_DEVICE_COUNT_DESC:
			case Pp_AdminClientManager::DEVICE_INFO_SORT_ITEM_DEVICE_PERCENTAGE_DESC:
				$direction = 'desc';
				break;
		}
			
		// DBから取得
		$tmp_list = $client_m->getKpiDeviceInfoListEx($period, ($search_ua ? $search_ua : null), 'device_count', $direction);
		
		// 連想配列から配列へ変換
		$table = array(array(
			'デバイスのモデル',
			'OSヴァージョン',
			'システムのメモリ量',
			'台数',
			'割合',
		));
		
		if (is_array($tmp_list) && !empty($tmp_list)) {
			while ($row = array_shift($tmp_list)) {
				$table[] = array(
					$row['device_model'],
					$row['operating_system'],
					$row['system_memory_size'],
					$row['device_count'],
					$row['device_percentage'],
				);
			}
		}

		// テンプレート変数へセット
		$this->af->setApp('table', $table);
		
		if ($format == 'csv') {
			$this->af->setApp('filename', 'device_info_' . $period . '_' . $search_ua . '_' . date('YmdHis', $_SERVER['REQUEST_TIME']) . '.csv');
		} else {
			$this->af->setApp('search_flg', '1');
		}
		
        return 'admin_kpi_device_info_index';
    }
}

?>