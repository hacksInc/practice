<?php
/**
 *  Admin/Developer/Gacha/Weight/Category/Log/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_gacha_weight_category_log_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperGachaWeightCategoryLogView extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$admin_m =& $this->backend->getManager('Admin');
		
		$gacha_id = $this->af->get('gacha_id');
		$limit = 200;
		$limit_tmp = $gacha_id ? 9999 : $limit;
		
		$list = $admin_m->getAdminOperationLogReverse('/developer/gacha', 'category_log', $limit_tmp);
		foreach ($list as $i => $row) {
			if ($gacha_id && ($gacha_id != $row['gacha_id'])) {
				unset($list[$i]);
				continue;
			}
			
			if ($i >= $limit) {
				break;
			}
			
			switch ($row['action']) {
				case 'admin_developer_gacha_weight_category_create_exec':
					$action_type = '追加';
					break;
				
				case 'admin_developer_gacha_weight_category_update_multi_exec':
					$action_type = '修正';
					break;
				
				case 'admin_developer_gacha_weight_category_delete_exec':
					$action_type = '削除';
					break;
				
				default:
					$action_type = null;
			}
			
			if ($action_type) {
				$list[$i]['action_type'] = $action_type;
			}
		}
		
		$this->af->setApp('list', $list);
		$this->af->setApp('form_template', $this->af->form_template);
    }
}

?>