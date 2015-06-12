<?php
/**
 *  Admin/Developer/Master/Editlog/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_editlog_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterEditlogView extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$admin_m =& $this->backend->getManager('Admin');
		$developer_m =& $this->backend->getManager('Developer');
		
		$table = $this->af->get('table');
		$table_label = $developer_m->getMasterTableLabel($table);
		
		$labels = array();
		
		$limit = 200;
		$list = $admin_m->getAdminOperationLogReverse('/api/rest', $table, $limit);
		foreach ($list as $i => $row) {
			// methodに応じて表示用ラベルを用意
			switch ($row['method']) {
				case 'POST':
					$method_label = '追加';
					break;
				
				case 'PATCH':
					$method_label = '変更';
					break;
				
				case 'DELETE':
					$method_label = '削除';
					break;
				
				default:
					$method_label = null;
			}
			
			if ($method_label) {
				$list[$i]['method_label'] = $method_label;
			}

			// http_statusに応じて表示用ラベルを用意
			switch ($row['http_status']) {
				case 204:
					$http_status_label = 'OK';
					break;
				
				case 500:
					$http_status_label = 'ERROR';
					break;
				
				default:
					$http_status_label = null;
					break;
			}

			if ($http_status_label) {
				$list[$i]['http_status_label'] = $http_status_label;
			}
			
			foreach ($row as $label => $value) {
				switch ($label) {
					case 'time':
					case 'user':
					case 'method':
					case 'http_status':
					case 'id':
						break;
					
					default:
						if (!in_array($label, $labels)) {
							$labels[] = $label;
						}
				}
			}
		}
		
		$this->af->setApp('limit',      $limit);
		$this->af->setApp('list',       $list);
		$this->af->setApp('labels',     $labels);
		$this->af->setApp('labels_cnt', count($labels));

		$this->af->setApp('table_label', $table_label);
		$this->af->setApp('table',       $table);

	}
}

?>