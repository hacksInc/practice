<?php
/**
 *  Admin/Present/Distribution/Content/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_present_distribution_content_create_input view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminPresentDistributionContentCreateInput extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_present_distribution_content_create_exec' => null,
	);

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$present_m =& $this->backend->getManager('AdminPresent');
		$item_m =& $this->backend->getManager('Item');
		$present_mng_id = $this->af->get('present_mng_id');
		if ($present_mng_id >= 0) {
			$row = $present_m->getPresentMng($present_mng_id);
		}
		//error_log(print_r($row,true));

		// 表示終了日時デフォルト値の設定
		if ($present_mng_id) {
			$access_date_start = $row['access_date_start'];
			$access_date_end = $row['access_date_end'];
			$distribute_date_start = $row['distribute_date_start'];
			$distribute_date_end = $row['distribute_date_end'];
			$num = $row['num'];
			if ($row['present_category'] == $present_m->CATEGORY_PHOTO)
			{
				$item_id = $row['present_value'];
			} else $item_id = 0;
			$lv = $row['lv'];
			$comment_id = $row['comment_id'];
			$comment = $row['comment'];
		} else {
			$present_mng_id = -1;
			$access_date_start = date('Y-m-d', $_SERVER['REQUEST_TIME']) . ' 00:00:00';
			$access_date_end = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
			$distribute_date_start = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] + 600);
			$distribute_date_end = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] + 3600*12);
			$num = 1;
			$item_id = 0;
			$lv = 1;
			$comment_id = 1;
			$comment = '';
		}

		$this->af->setApp('present_mng_id', $present_mng_id);
		$this->af->setApp('access_date_start', $access_date_start);
		$this->af->setApp('access_date_end', $access_date_end);
		$this->af->setApp('distribute_date_start', $distribute_date_start);
		$this->af->setApp('distribute_date_end', $distribute_date_end);
		$this->af->setApp('present_value_options', $item_m->ITEM_ID_OPTIONS);
		$this->af->setApp('comment_id_options', $present_m->COMMENT_ID_OPTIONS);
		$this->af->setApp('num', $num);
		$this->af->setApp('item_id', $item_id);
		$this->af->setApp('lv', $lv);
		$this->af->setApp('comment_id', $comment_id);
		$this->af->setApp('comment', $comment);
	}
}

