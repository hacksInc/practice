<?php
/**
 *  Admin/Developer/Master/Edit.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_edit view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterEdit extends Pp_AdminViewClass
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
		
		$list = $developer_m->getMasterList($table);
		$label = $developer_m->getMasterColumnsLabel($table);
		$table_label = $developer_m->getMasterTableLabel($table);

		// EditableGridのメタデータを取得する
		$metadata = $developer_m->getEditableGridMetadata($table);
		$metadata_add = $developer_m->getEditableGridMetadata($table, true);
		
		// EditableGridのデータを組み立てる
		$data = array();
//		$i = 0;
		foreach ($list as $row) {
//			$id = ++$i;
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
			
			$values[$row['name']] = '';
		}
		
		$data_add = array(array(
			'id' => 1,
			'values' => $values,
		));
		
//		$this->af->setApp('label',    $label);
		$this->af->setAppNe('metadata',     json_encode($metadata));
		$this->af->setAppNe('data',         json_encode($data));
		$this->af->setAppNe('metadata_add', json_encode($metadata_add));
		$this->af->setAppNe('data_add',     json_encode($data_add));
		$this->af->setAppNe('primary_key_positions', json_encode($developer_m->getPrimaryKeyPositions($table)));
		$this->af->setApp('table_label', $table_label);
		$this->af->setApp('table',       $table);
		
		parent::preforward();
    }
}

?>
