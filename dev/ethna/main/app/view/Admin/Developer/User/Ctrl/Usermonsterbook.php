<?php
/**
 *  Admin/Developer/User/Ctrl/Usermonsterbook.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_ctrl_usermonsterbook view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperUserCtrlUsermonsterbook extends Pp_AdminViewClass
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
		$monster_m =& $this->backend->getManager('AdminMonster');

		$table = $this->af->get('table');
		$user_id = $this->af->get('id');
		
		$user_base = $user_m->getUserBase($user_id);
		$book_list = $monster_m->getUserMonsterBookAssoc($user_id);
		$monster_master = $monster_m->getMasterMonsterAssoc();
		
		/*
		$user_book = array();
		foreach($book_list as $key => $val) {
			$monster_id = $val['monster_id'];
			foreach($monster_master as $mkey => $mval) {
				if ($mkey == $monster_id) {
					$val['name'] = $mval['name_ja'];
					break;
				}
			}
			$user_book[] = $val;
		}
		*/
		
//		$list = $developer_m->getMasterList($table);
		$list = $developer_m->getUserList($table, $user_id);
	//error_log("$table:$user_id:list=".print_r($list,true));
		$label = $developer_m->getMasterColumnsLabel($table);
		$table_label = $developer_m->getMasterTableLabel($table);

		// EditableGridのメタデータを取得する
		$metadata = $developer_m->getEditableGridMetadata($table);
	//	$metadata_add = $developer_m->getEditableGridMetadata($table, true);
		
		// EditableGridのデータを組み立てる
		$data = array();
		$idx=1;
		foreach ($list as $row) {
			$id = $developer_m->getRowIdFromAssoc($table, $row, ',');

			$values = array();
			foreach ($label as $key => $dummy) {
				$values[$key] = $row[$key];
			}
			$values['no'] = $idx++;
			foreach($monster_master as $mkey => $mval) {
				if ($values['monster_id'] == $mkey) {
					$values['name'] = $mval['name_ja'];
					break;
				}
			}
			
			$data[] = array('id' => $id, 'values' => $values);
		}
	//error_log("metadata=".print_r($metadata,true));
		$metadata2 = array();
		$metadata2[] = array(
			'name'     => 'no',
			'label'    => 'No.',
			'datatype' => 'number',
			'editable' => 0,
		);
		$metadata2[] = $metadata[0];//monster_id
		$metadata2[] = array(
			'name'     => 'name',
			'label'    => 'モンスター名',
			'datatype' => 'string',
			'editable' => 0,
		);
		$metadata2[] = $metadata[1];//status
	/*
		$metadata2[] = array(
			'name'     => 'status',
			'label'    => '入手',
			'datatype' => 'number',
			'editable' => 1,
		);
	*/
		$metadata2[] = $metadata[2];//遭遇日時
		$metadata2[] = $metadata[3];//取得日時
		$metadata2[] = $metadata[4];//action
	//error_log("metadata2=".print_r($metadata2,true));
		
	//error_log("data=".print_r($data,true));
		
		/*
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
		*/
		
		// 主キーがuser_idのみのテーブルは新規追加対象外
//		$primary_keys = $developer_m->getPrimaryKeys($table);
//		if ((count($primary_keys) == 1) && ($primary_keys[0] == 'user_id')) {
//			$creatable = false;
//		} else {
//			$creatable = true;
//		}
$creatable = false;
		
		$this->af->setAppNe('metadata',     json_encode($metadata2));
		$this->af->setAppNe('data',         json_encode($data));
	//	$this->af->setAppNe('metadata_add', json_encode($metadata_add));
	//	$this->af->setAppNe('data_add',     json_encode($data_add));
		$this->af->setAppNe('primary_key_positions', json_encode($developer_m->getPrimaryKeyPositions($table)));
		$this->af->setApp('table_label', $table_label);
		$this->af->setApp('table',       $table);
		$this->af->setApp('creatable',   $creatable);
		
		$this->af->setApp('base', $user_base);
	//	$this->af->setApp('book', $user_book);
		
		parent::preforward();
	}
}

?>
