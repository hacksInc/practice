<?php
/**
 *  Admin/Present/Distribution/Content/Bulk/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_present_distribution_content_bulk_confirm view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminPresentDistributionContentBulkConfirm extends Pp_AdminViewClass
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
		$user_m =& $this->backend->getManager('User');

		$ppid_list = $this->af->get('ppid_list');
		$ppids = explode("\r\n", $ppid_list);
		$pp_ids = array();
		//商用Main環境だけ判定を行う
		//$http_host = $_SERVER['HTTP_HOST'];
		//list($host, $port) = explode(':', $http_host, 2);
		//$unit = $this->session->get('unit');
		$is_regist = true;
		foreach($ppids as $key => $val) {
			error_log("ppid $key=$val");
			//$pp_ng = 0;
			$user_ng = 0;
			$user_fmt_ng = 0;
			$user_exists = 0;
			$pp_id = $val;
			/*
			if (strcmp($host, 'main.mgr.jmja.jugmon.net') == 0) {
				if ($unit == 1 && !($pp_id >= 810000000 && $pp_id < 820000000)) $pp_ng = 1;
				if ($unit == 2 && !($pp_id >= 820000000 && $pp_id < 830000000)) $pp_ng = 1;
			}
			*/
			//$pp_ng = 1;
			
			$user = '';
			if (is_numeric($pp_id)) {
				$user = $user_m->getUserBase($pp_id);
				if (isset($user['pp_id'])) {
					$user = $user_m->getUserBase($pp_id);
					$this->af->setApp('nickname', $user['name']);
				} else {
					$user_exists = 1;
					if (Util::getAppverEnv() == 'main') {
						$ppid_range = $this->config->get( 'ppid_range' );
						if (!($pp_id >= $ppid_range['min'] && $pp_id <= $ppid_range['max'])) {
							$user_ng = 1;
						}
					}
				}
			} else {
				$user_fmt_ng = 1;
			}
			
			/*
			if ($pp_ng == 0) {
				$user = $user_m->getUserBase($pp_id);
				$nickname = $user['name'];
			} else {
				$nickname = '';
			}
			$pp_ids[$key]['nickname'] = $nickname;
			*/

			$pp_ids[$key]['nickname'] = $user['name'];
			$pp_ids[$key]['ppid'] = $pp_id;
			//$pp_ids[$key]['ng'] = $pp_ng;
			$pp_ids[$key]['user_ng'] = $user_ng;
			$pp_ids[$key]['user_fmt_ng'] = $user_fmt_ng;
			$pp_ids[$key]['user_exists'] = $user_exists;

			if ($is_regist) {
				$is_regist = (!$user_ng && !$user_fmt_ng && !$user_exists);
			}
		}

		$this->af->setApp('pp_ids', $pp_ids);
		$this->af->setApp('is_regist', $is_regist);

		$present_value = (int)$this->af->get('present_value');
		if ($present_value == Pp_ItemManager::ITEM_ID_PHOTO)
		{
			// フォト
			$present_category = Pp_PresentManager::CATEGORY_PHOTO;
			$photo_m =& $this->backend->getManager('Photo');
			$photo = $photo_m->getMasterPhotoByPhotoId($this->af->get('item_id'));
			$this->af->setApp('photo_name', $photo['voice_name']);
		} else if ($present_value == Pp_ItemManager::ITEM_ID_PORTAL_POINT) {
			// ポータルポイント
			$present_category = Pp_PresentManager::CATEGORY_PP;
		} else {
			// 通常アイテム
			$present_category = Pp_PresentManager::CATEGORY_ITEM;
		}
		$this->af->setApp('present_value', $item_m->ITEM_ID_OPTIONS[$present_value]);
		$this->af->setApp('present_category', $present_category);
		$this->af->setApp('comment_id', $present_m->COMMENT_ID_OPTIONS[($this->af->get('comment_id'))]);
		$this->af->setApp('form_template', $this->af->form_template);
	}
}

