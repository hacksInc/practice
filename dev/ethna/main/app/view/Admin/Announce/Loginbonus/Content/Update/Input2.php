<?php
/**
 *  Admin/Announce/Loginbonus/Content/Update/Input2.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_loginbonus_content_update_input2 view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceLoginbonusContentUpdateInput2 extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$developer_m =& $this->backend->getManager('Developer');
		$present_m =& $this->backend->getManager('Present');

		//入力フォームの値を配列にする
		$data = array();
		for ($i = 0; $i < 10; $i++) {
			$data[$i] = array();
			foreach(array('dist_type','number','item_id','lv') as $val) {
				$data[$i][$val] = $this->af->get("$val$i");
			}
		}
		$this->af->setApp('data', $data);
		$this->af->setApp('dist_type_options', $present_m->DIST_TYPE_OPTIONS);
		
		parent::preforward();
    }
}
?>