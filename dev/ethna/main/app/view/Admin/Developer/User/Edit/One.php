<?php
/**
 *  Admin/Developer/User/Edit/One.php
 *
 *  1行を編集できるだけのビュー。
 *  Pp_Action_AdminDeveloperUserEditから呼ばれる。
 *  t_user_baseのカラム数が多くてEditableGridを使用すると表示が横に伸びすぎるので、
 *  EditableGridは使用せず、1行についての各カラム内容を縦に並べて表示する為に、このビューを作成した。
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_user_edit_one view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperUserEditOne extends Pp_AdminViewClass
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
		
		$list = $developer_m->getUserList($table, $user_id);
		$label = $developer_m->getMasterColumnsLabel($table);
		$table_label = $developer_m->getMasterTableLabel($table);
		
		$this->af->setApp('table_label', $table_label);
		$this->af->setApp('table',       $table);
		$this->af->setApp('label',       $label);
		$this->af->setApp('user',        $list[$user_id]);
		$this->af->setAppNe('user_json', json_encode($list[$user_id]));
		
		parent::preforward();
    }
}
?>