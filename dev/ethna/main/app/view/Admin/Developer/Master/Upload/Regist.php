<?php
/**
 *  Admin/Developer/Master/Upload/Regist.php
 *
 *  このビューはadmin_developer_master_upload_registアクション以外からも呼ばれる。
 *  （他のアクションから、Pp_AdminActionClass の performMasterUploadRegist 経由で、このビューへ遷移する）
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_master_upload_regist view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperMasterUploadRegist extends Pp_AdminViewClass
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

		$table_label = $developer_m->getMasterTableLabel($table);

		$this->af->setApp('table_label', $table_label);
    }
}

?>