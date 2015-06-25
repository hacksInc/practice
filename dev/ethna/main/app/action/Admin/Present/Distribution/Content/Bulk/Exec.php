<?php
/**
 *  Admin/Present/Distribution/Content/Bulk/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_present_distribution_content_bulk_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminPresentDistributionContentBulkExec extends Pp_Form_AdminPresentDistributionContent
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'pp_id',
		'comment_id',
		'comment',
		'present_category',
		'present_value',
		'item_id',
		'lv',
		'num',
		'ppid_list',
	);
}

/**
 *  admin_present_distribution_content_bulk_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminPresentDistributionContentBulkExec extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_present_distribution_content_bulk_exec Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
/*
	function prepare()
	{
		// アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
	}
 */

	/**
	 *  admin_present_distribution_content_bulk_exec action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$present_m =& $this->backend->getManager('AdminPresent');
		$item_m =& $this->backend->getManager('Item');
		$user_m =& $this->backend->getManager('User');
		$admin_m =& $this->backend->getManager('Admin');

		//直接プレゼントBOXに入れる
		$ppid_list = $this->af->get('ppid_list');
		$ppids = explode("\r\n", $ppid_list);
		$pp_ids = array();
		//商用Main環境だけ判定を行う
		//$http_host = $_SERVER['HTTP_HOST'];
		//list($host, $port) = explode(':', $http_host, 2);
		//$unit = $this->session->get('unit');
		foreach($ppids as $key => $val) {
			$result = 0;
			$pp_ng = 0;
			$pp_id = $val;
/*
			if (strcmp($host, 'main.mgr.jmja.jugmon.net') == 0) {
				if ($unit == 1 && !($pp_id >= 810000000 && $pp_id < 820000000)) $pp_ng = 1;
				if ($unit == 2 && !($pp_id >= 820000000 && $pp_id < 830000000)) $pp_ng = 1;
			}
*/
			//$pp_ng = 1;
			//if ($pp_ng == 0) {
				$user = $user_m->getUserBase($pp_id);
				$nickname = $user['name'];
				//プレゼント配布する
				$f_present_category = $this->af->get('present_category');
				if ($f_present_category == Pp_PresentManager::CATEGORY_PHOTO)
				{
					$f_present_value = $this->af->get('item_id');
				} else {
					$f_present_value = $this->af->get('present_value');
				}
				$f_lv = $this->af->get('lv');
				$f_num = $this->af->get('num');
				$f_comment = $this->af->get('comment');
				$f_comment_id = $this->af->get('comment_id');

				//プレゼントのデータをセット
				$present = array(
					'pp_id'			=> $pp_id,
					'comment_id'		=> $f_comment_id,
					'comment'		=> $f_comment,
					'present_category'	=> $f_present_category,
					'present_value'		=> $f_present_value,
					'lv'			=> $f_lv,
					'num'			=> $f_num,
				);

				//プレゼントを贈る
				//$ret = $present_m->setUserPresent(Pp_PresentManager::PPID_FROM_ADMIN, Pp_PresentManager::ID_NEW_PRESENT, $present);
				$ret = $present_m->setUserPresent($pp_id, Pp_PresentManager::ID_NEW_PRESENT, $present);
				if (!$ret || Ethna::isError($ret)) {
					error_log("MNG ERR present_bulk $pp_id ");
					$result = -1;
				} else {
					error_log("MNG SUCCESS present_bulk $pp_id");
					$result = 0;
				}

			//} else {
				//$nickname = '';
			//}
			//$pp_ids[$key]['nickname'] = $nickname;
			$pp_ids[$key]['nickname'] = $user['name'];
			$pp_ids[$key]['ppid'] = $pp_id;
			$pp_ids[$key]['ng'] = $pp_ng;
			$pp_ids[$key]['result'] = $result;
		}
		$this->af->setApp('pp_ids', $pp_ids);

		$present_category = $this->af->get('present_category');
		if ($present_category == Pp_PresentManager::CATEGORY_PHOTO) {
			$photo_m =& $this->backend->getManager( 'Photo' );
			$photo = $photo_m->getMasterPhotoByPhotoId($this->af->get('item_id'));
			$this->af->setApp('photo_name', $photo['voice_name']);
			$this->af->setApp('present_value', $item_m->ITEM_ID_OPTIONS[Pp_ItemManager::ITEM_ID_PHOTO]);
			$this->af->setApp('item_id', $this->af->get('item_id'));
		} else {
			$this->af->setApp('photo_name', '');
			$this->af->setApp('present_value', $item_m->ITEM_ID_OPTIONS[$this->af->get('present_value')]);
			$this->af->setApp('item_id', 0);
		}
		$this->af->setApp('comment_id', $present_m->COMMENT_ID_OPTIONS[($this->af->get('comment_id'))]);
		$this->af->setApp('form_template', $this->af->form_template);

		return 'admin_present_distribution_content_bulk_exec';
	}
}

