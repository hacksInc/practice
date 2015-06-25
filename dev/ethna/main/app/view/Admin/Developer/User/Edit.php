<?php
/**
 *  Admin/Developer/User/Edit.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_edit view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperUserEdit extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$developer_m =& $this->backend->getManager('Developer');

		$table = $this->af->get('table');
		$user_id = $this->af->get('id');
		
//		$list = $developer_m->getMasterList($table);
		$list = $developer_m->getUserList($table, $user_id);
		$label = $developer_m->getMasterColumnsLabel($table);
		$table_label = $developer_m->getMasterTableLabel($table);

		// EditableGridのメタデータを取得する
		$metadata = $developer_m->getEditableGridMetadata($table);
		$metadata_add = $developer_m->getEditableGridMetadata($table, true);
		
		// EditableGridのデータを組み立てる
		$data = array();
		foreach ($list as $row) {
			$id = $developer_m->getRowIdFromAssoc($table, $row, ',');

			$values = array();
			foreach ($label as $key => $dummy) {
				$values[$key] = $row[$key];
			}
			
			$data[] = array('id' => $id, 'values' => $values);
		}
		
		// 新規追加用
		$values = array();
		foreach ($metadata_add as $i => $row) {
			if ($row['name'] =='action') {
				continue;
			}
			
			// user_idは既定値をセット＆編集不可に
			// ただしユーザー間の関係をあらわすテーブル（t_user_friend等）は編集可に
			if (($row['name'] == 'user_id') && ($table != 't_user_friend')) {
				$values[$row['name']] = $user_id;
				$metadata_add[$i]['editable'] = false;
			} else {
				$values[$row['name']] = '';
			}
		}
		
		$data_add = array(array(
			'id' => 1,
			'values' => $values,
		));
		
		// 主キーがuser_idのみのテーブルは新規追加対象外
//		$primary_keys = $developer_m->getPrimaryKeys($table);
//		if ((count($primary_keys) == 1) && ($primary_keys[0] == 'user_id')) {
//			$creatable = false;
//		} else {
//			$creatable = true;
//		}
$creatable = true;
		
		$this->af->setAppNe('metadata',     json_encode($metadata));
		$this->af->setAppNe('data',         json_encode($data));
		$this->af->setAppNe('metadata_add', json_encode($metadata_add));
		$this->af->setAppNe('data_add',     json_encode($data_add));
		$this->af->setAppNe('primary_key_positions', json_encode($developer_m->getPrimaryKeyPositions($table)));
		$this->af->setApp('table_label', $table_label);
		$this->af->setApp('table',       $table);
		$this->af->setApp('creatable',   $creatable);
		
		parent::preforward();
	}
}

?>
