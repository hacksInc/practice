<?php
/**
 *  Admin/Present/Distribution/Content/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_present_distribution_content_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminPresentDistributionContentIndex extends Pp_AdminViewClass
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
		$photo_m =& $this->backend->getManager('Photo');
		$user_m =& $this->backend->getManager('User');

		$date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

		$list = $present_m->getPresentMngList(0,100);
		//error_log(print_r($list,true));
		if ($list) foreach ($list as $i => $row) {
			if ($row['target_type'] == Pp_PresentManager::TARGET_TYPE_PPID) {
				$user = $user_m->getUserBase($row['pp_id']);
				$nickname = $user['name'];
			} else $nickname = '';
			$list[$i]['nickname'] = $nickname;
			if ($row['present_category'] == Pp_PresentManager::CATEGORY_PHOTO) {
				$photo = $photo_m->getMasterPhotoByPhotoId($row['present_value']);
				$list[$i]['photo_name'] = $photo['voice_name'];
				$list[$i]['present_values'] = $item_m->ITEM_ID_OPTIONS[Pp_ItemManager::ITEM_ID_PHOTO];
				$list[$i]['item_id'] = $list[$i]['present_value'];
			} else {
				$list[$i]['photo_name'] = '';
				$list[$i]['present_values'] = $item_m->ITEM_ID_OPTIONS[($list[$i]['present_value'])];
				$list[$i]['item_id'] = 0;
			}
			$list[$i]['comment_ids'] = $present_m->COMMENT_ID_OPTIONS[($list[$i]['comment_id'])];
			$list[$i]['dist_term'] = 0;
			if ($list[$i]['date_dist_start'] <= date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) && $list[$i]['date_dist_end'] > date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])) $list[$i]['dist_term'] = 1;
		}

		$this->af->setApp('list', $list);

		$this->af->setApp('form_template', $this->af->form_template);
	}
}

