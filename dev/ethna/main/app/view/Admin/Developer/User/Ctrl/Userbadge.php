<?php
/**
 *  Admin/Developer/User/Ctrl/Userbadge.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_ctrl_userbadge view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperUserCtrlUserbadge extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$developer_m =& $this->backend->getManager('Developer');
		$user_m =& $this->backend->getManager('User');
		$badge_m =& $this->backend->getManager('Badge');

		$table = $this->af->get('table');
		$user_id = $this->af->get('id');
		
		$user_base = $user_m->getUserBase($user_id);
		$badge_master = $badge_m->getMasterBadge();
		$badge_list = $badge_m->getUserBadgeList($user_id);
		
		$user_badge = array();
		foreach($badge_list as $key => $val) {
			$badge_id = $val['badge_id'];
			foreach($badge_master as $mkey => $mval) {
				if ($mval['badge_id'] == $badge_id) {
					$val['name'] = $mval['name_ja'];
					break;
				}
			}
			$user_badge[] = $val;
		}
		
//		$list = $developer_m->getMasterList($table);
		$list = $developer_m->getUserList($table, $user_id);
		$label = $developer_m->getMasterColumnsLabel($table);
		$table_label = $developer_m->getMasterTableLabel($table);

		// EditableGridのメタデータを取得する
		$metadata = $developer_m->getEditableGridMetadata($table);
		$metadata_add = $developer_m->getEditableGridMetadata($table, true);
		
		// EditableGridのデータを組み立てる
		$data = array();
		$idx=1;
		if (!empty($list)) {
			foreach ($list as $row) {
				$id = $developer_m->getRowIdFromAssoc($table, $row, ',');
				$values = array();
				foreach ($label as $key => $dummy) {
					$values[$key] = $row[$key];
				}
				$values['no'] = $idx++;
				foreach($badge_master as $mkey => $mval) {
					if ($mval['badge_id'] == $values['badge_id']) {
						$values['name'] = $mval['name_ja'];
						break;
					}
				}
				
				$data[] = array('id' => $id, 'values' => $values);
			}
		}
	//error_log("metadata=".print_r($metadata,true));
		$metadata2 = array();
		$metadata2[] = array(
			'name'     => 'no',
			'label'    => 'No.',
			'datatype' => 'number',
			'editable' => 0,
		);
		$metadata2[] = $metadata[1];//badge_id
		$metadata2[] = array(
			'name'     => 'name',
			'label'    => 'バッジ名',
			'datatype' => 'string',
			'editable' => 0,
		);
		$metadata2[] = array(
			'name'     => 'num',
			'label'    => '個数',
			'datatype' => 'number',
			'editable' => 1,
		);
		$metadata2[] = $metadata[3];//action
	//error_log("metadata2=".print_r($metadata2,true));
		
	//error_log("data=".print_r($data,true));
		
		
		// 新規追加用
		$values = array();
		if (!empty($metadata_add)) {
			foreach ($metadata_add as $i => $row) {
				if ($row['name'] =='action') {
					continue;
				}
				
				if ($row['name'] == 'user_id') {
					$values[$row['name']] = $user_id;
					$metadata_add[$i]['editable'] = false;
				} else {
					$values[$row['name']] = '';
				}
			}
		}
		
		$data_add = array(array(
			'id' => 1,
			'values' => $values,
		));
		
		$creatable = true;
		
		$this->af->setAppNe('metadata',     json_encode($metadata2));
		$this->af->setAppNe('data',         json_encode($data));
		$this->af->setAppNe('metadata_add', json_encode($metadata_add));
		$this->af->setAppNe('data_add',     json_encode($data_add));
		$this->af->setAppNe('primary_key_positions', json_encode($developer_m->getPrimaryKeyPositions($table)));
		$this->af->setApp('table_label', $table_label);
		$this->af->setApp('table',       $table);
		$this->af->setApp('creatable',   $creatable);
		
		$this->af->setApp('base', $user_base);
		$this->af->setApp('badge', $user_badge);
		
		parent::preforward();
	}
}

?>
