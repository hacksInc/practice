<?php
/**
 *  Admin/Present/Distribution/Content/Bulk/Input2.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_present_distribution_content_bulk_input2 view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminPresentDistributionContentBulkInput2 extends Pp_AdminViewClass
{
	/**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$present_m =& $this->backend->getManager('AdminPresent');
		$item_m =& $this->backend->getManager('Item');

		// デフォルト値の設定
	//	$num = 1;
	//	$present_value = 0;
	//	$lv = 1;
	//	$comment_id = 1;
	//	$comment = '';

		$this->af->setApp('present_value_options', $item_m->ITEM_ID_OPTIONS);
		$this->af->setApp('comment_id_options', $present_m->COMMENT_ID_OPTIONS);
	//	$this->af->setApp('num', $num);
	//	$this->af->setApp('present_value', $present_value);
	//	$this->af->setApp('lv', $lv);
	//	$this->af->setApp('comment_id', $comment_id);
	//	$this->af->setApp('comment', $comment);
    }
}

