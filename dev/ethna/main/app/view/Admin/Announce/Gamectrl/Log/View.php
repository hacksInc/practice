<?php
/**
 *  Admin/Announce/Gamectrl/Log/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_announce_gamectrl_log_view view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminAnnounceGamectrlLogView extends Pp_AdminViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$admin_m =& $this->backend->getManager('Admin');

		$list = (array)$admin_m->getAdminOperationLogReverse('/announce/gamectrl', 'content_log', 200);
		foreach ($list as $i => $row) {
			switch ($row['action']) {
				case 'maintenance_before':
					$action_type = 'メンテナンス開始';
					break;

				case 'maintenance_stop':
					$action_type = 'メンテナンス中止';
					break;

				case 'date_end_update':
					$action_type = '終了時刻変更';
					break;

				case 'date_end_update_and_stop':
					$action_type = '終了時刻変更(メンテ中止)';
					break;

				case 'btf_set':
					$action_type = 'メンテ突破フラグ設定';
					break;

				case 'btf_reset':
					$action_type = 'メンテ突破フラグ解除';
					break;

				default:
					$action_type = null;
			}

			if ($action_type) {
				$list[$i]['action_type'] = $action_type;
			}

			if (isset($row['lang']) && isset($row['ua'])) {
				$list[$i]['lu'] = $row['lang'] . $row['ua'];
			}
		}

		$this->af->setApp('list', $list);
		$this->af->setApp('form_template', $this->af->form_template);
	}
}
