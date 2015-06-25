<?php
/**
 *  Admin/Developer/Master/List.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_list view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterList extends Pp_AdminViewClass
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

		$list        = $developer_m->getMasterList($table);
		$label       = $developer_m->getMasterColumnsLabel($table);
		$table_label = $developer_m->getMasterTableLabel($table);
		
		$this->af->setApp('list',        $list);
		$this->af->setApp('label',       $label);
		$this->af->setApp('table_label', $table_label);
		
		parent::preforward();
    }
}

?>
