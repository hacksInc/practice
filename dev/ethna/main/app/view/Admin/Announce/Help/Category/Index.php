<?php
/**
 *  Admin/Announce/Help/Category/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_help_category_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceHelpCategoryIndex extends Pp_AdminViewClass
{
	var $helper_action_form = array(
		'admin_announce_help_category_create_exec' => null,
	);

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$help_m =& $this->backend->getManager('AdminHelp');
		$user_m =& $this->backend->getManager('User');
		$admin_m =& $this->backend->getManager('Admin');

		$list = $help_m->getHelpCategoryList(0, 100000, false);
		//error_log(var_export($list, true));

		if ($list) foreach ($list as $i => $row) {
			if ($row['date_start'] > date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])) {
				$status = 'waiting';
			} else if ($row['date_end'] > date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])) {
				$status = 'active';
			} else {
				$status = null;
			}

			$list[$i]['status'] = $status;

			$list[$i]['date_disp_short'] = $help_m->getDateDispShort($row['date_disp']);
		}

		// テスト表示リンク関連
		$appver_env = Util::getAppverEnv();
		$appver = $admin_m->getAppverFromAppverEnv($appver_env);
		$resource_disabled = false;
		if (($appver_env == 'review') && !$appver) {
			$resource_disabled = true;
		}

		$this->af->setApp('list', $list);
		$this->af->setAppNe('list', $list);

		$this->af->setApp('form_template', $this->af->form_template);
		$this->af->setApp('resource_host', Util::getResourceHttpHostFromAdminHttpHost());
		$this->af->setApp('resource_disabled', $resource_disabled);
		$this->af->setApp('appver', $appver);
	}
}
