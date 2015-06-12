<?php
/**
 *  Admin/Present/Distribution/Content/History.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pager.php';
require_once 'Pp_AdminViewClass.php';

/**
 *  admin_present_distribution_content_history view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminPresentDistributionContentHistory extends Pp_AdminViewClass
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
		$user_m =& $this->backend->getManager('User');
		$photo_m =& $this->backend->getManager('Photo');
		$page = $this->af->getPageFromPageID();

		$limit = 100;
		$offset = $limit * $page;
		$date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

		$list_all = $present_m->getPresentMngList(0, 1000);
		$list = array_slice($list_all, $offset, $limit);
		if ($list) foreach ($list as $i => $row) {
			if ($row['target_type'] == Pp_PresentManager::TARGET_TYPE_PPID)
			{
				$user = $user_m->getUserBase($row['pp_id']);
				$nickname = $user['name'];
			} else $nickname = '';
			$list[$i]['nickname'] = $nickname;

			if ($row['present_category'] == Pp_PresentManager::CATEGORY_PHOTO)
			{
				$photo = $photo_m->getMasterPhotoByPhotoId($row['present_value']);
				$list[$i]['photo_name'] = $photo['voice_name'];
				$list[$i]['present_values'] = $item_m->ITEM_ID_OPTIONS[Pp_ItemManager::ITEM_ID_PHOTO];
			} else {
				$list[$i]['photo_name'] = '';
				$list[$i]['present_values'] = $item_m->ITEM_ID_OPTIONS[($list[$i]['present_value'])];
			}
			$list[$i]['comment_ids'] = $present_m->COMMENT_ID_OPTIONS[($list[$i]['comment_id'])];
			$list[$i]['dist_term'] = 0;
			if ($list[$i]['distribute_date_start'] <= $date && $list[$i]['distribute_date_end'] > $date)
			{
				$list[$i]['dist_term'] = 1;
			}
		}

		$num = count($list_all);

		$options = array(
			'mode'        => 'Sliding',
			'delta'       => 4,
			'importQuery' => false,
			'totalItems'  => $num,
			'perPage'     => $limit,
		);

		$pager =& Pager::factory($options);
		$links = $pager->getLinks();

		// テンプレート変数にアサイン
		$this->af->setApp('list', $list);

		$this->af->setApp('form_template', $this->af->form_template);

		$this->af->setAppNe('pager', $links);
	}
}

