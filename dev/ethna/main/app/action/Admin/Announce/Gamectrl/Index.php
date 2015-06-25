<?php
/**
 *  Admin/Announce/Gamectrl/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_gamectrl_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceGamectrlIndex extends Pp_AdminActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'act',
		'date_mstart',
		'date_mend',
		'timechange',
		'unitsync',
		'btf',
	);
}

/**
 *  admin_announce_gamectrl_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceGamectrlIndex extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_gamectrl_index Action.
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
	 *  admin_announce_gamectrl_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$user_m =& $this->backend->getManager('User');
		$admin_m =& $this->backend->getManager('Admin');
		$lid = $this->session->get('lid');

		$unit_m =& $this->backend->getManager('Unit');
		$unitsync = $this->af->get('unitsync');
		$unit_all = $this->config->get('unit_all');
		$unit = $this->session->get('unit');
		$btf = $this->af->get('btf');
		$gm_ctrls = array();//他ユニットの状態
		$mes = array();
		$unit_stat = array();

		$columns = array();
		$null_chk = 0;
		$change_btf = false;
		foreach ($this->af->form as $key => $value) {
			$columns[$key] = $this->af->get($key);
			if (is_null($columns[$key])) $null_chk++;
		}

		$gm_ctrl = $user_m->getGameCtrl();

		$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$end = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] + 3600);

		//----------------------------------------------------------------------
		// データが存在していない場合に初期値を登録する処理
		//----------------------------------------------------------------------
		//空の場合
		if (empty($gm_ctrl)) {
			$ret = $user_m->insGameCtrl(array('date_start' => $now, 'date_end' => $now));
			if (!$ret || Ethna::isError($ret)) {
				return 'admin_error_500';
			}
			$mes[] = "unit $unit empty and insert new record";
		}

		//他のユニットも空かどうか調べる
		$sql = "SELECT * FROM ut_game_ctrl WHERE id = 1";
		foreach ($unit_all as $unit_no => $unit_info) {
			if ($unit_no != $unit) {
				$gm_ctrls[$unit_no] = $unit_m->getRowMultiUnit($sql, null, $unit_no, false);
				if ( Ethna::isError($gm_ctrls[$unit_no])) {
					return 'admin_error_500';
				}
				//空の場合
				if (empty($gm_ctrls[$unit_no])) {
					$param = array('date_start' => $now, 'date_end' => $now);
					$sql = "INSERT INTO ut_game_ctrl(id, status, date_start, date_end) VALUES(1, 0, ?, ?)";
					$ret = $unit_m->executeForUnit($unit_no, $sql, $param);
					if (!$ret || Ethna::isError($ret)) {
						return 'admin_error_500';
					}
					$mes[] = "unit $unit_no empty and insert new record";
				}
			}
		}

		$act = $columns['act'];
		$date_start = $columns['date_mstart'];
		$date_end = $columns['date_mend'];
		$timechange = $columns['timechange'];
		if (strlen($timechange) > 0) $act = $gm_ctrl['status'];
		$update = false;//更新フラグ

		//error_log("act=$act timechange=$timechange date_start=$date_start date_end=$date_end null=$null_chk");

		//----------------------------------------------------------------------
		// 現在時刻がメンテ開始時間が過ぎていた場合にステータスをメンテ中に更新する処理
		//----------------------------------------------------------------------
		//メンテ開始時間を過ぎていたら => メンテ中に更新
		if ($gm_ctrl['status'] == Pp_UserManager::GAME_CTRL_STATUS_MAINTENANCE_BEFORE && $gm_ctrl['date_start'] <= $now) {
			$gm_ctrl['status'] = Pp_UserManager::GAME_CTRL_STATUS_MAINTENANCE;
			$ret = $user_m->updGameCtrl($gm_ctrl);
			if (!$ret || Ethna::isError($ret)) {
				return 'admin_error_500';
			}
			$mes[] = "unit $unit maintenance started (start time passed)";
		}

		//他のユニットもメンテ開始時間を過ぎているか調べる
		foreach ($unit_all as $unit_no => $unit_info) {
			if ($unit_no != $unit) {
				//メンテ開始時間を過ぎていたら
				if ($gm_ctrls[$unit_no]['status'] == Pp_UserManager::GAME_CTRL_STATUS_MAINTENANCE_BEFORE && $gm_ctrls[$unit_no]['date_start'] <= $now) {
					$gm_ctrls[$unit_no]['status'] = Pp_UserManager::GAME_CTRL_STATUS_MAINTENANCE;
					$sql = "UPDATE ut_game_ctrl SET status = ?, date_start = ?, date_end = ? WHERE id = 1";
					$param = array($gm_ctrls[$unit_no]['status'],$gm_ctrls[$unit_no]['date_start'],$gm_ctrls[$unit_no]['date_end']);
					$ret = $unit_m->executeForUnit($unit_no, $sql, $param);
					if (!$ret || Ethna::isError($ret)) {
						return 'admin_error_500';
					}
					$mes[] = "unit $unit_no maintenance started (start time passed)";
				}
			}
		}

		//----------------------------------------------------------------------
		// 現在時刻がメンテ終了時間が過ぎていた場合にステータスを運用中に更新する処理
		//----------------------------------------------------------------------
		//メンテ終了時間を過ぎていたら => 運用中に更新
		if ($gm_ctrl['status'] == Pp_UserManager::GAME_CTRL_STATUS_MAINTENANCE && $gm_ctrl['date_end'] <= $now) {
			$gm_ctrl['status'] = Pp_UserManager::GAME_CTRL_STATUS_RUNNING;
			$ret = $user_m->updGameCtrl($gm_ctrl);
			if (!$ret || Ethna::isError($ret)) {
				return 'admin_error_500';
			}
			$mes[] = "unit $unit maintenance finished (end time passed)";
		}

		//他のユニットもメンテ終了時間を過ぎているか調べる
		foreach ($unit_all as $unit_no => $unit_info) {
			if ($unit_no != $unit) {
				//メンテ開始時間を過ぎていたら
				if ($gm_ctrls[$unit_no]['status'] == Pp_UserManager::GAME_CTRL_STATUS_MAINTENANCE && $gm_ctrls[$unit_no]['date_end'] <= $now) {
					$gm_ctrls[$unit_no]['status'] = Pp_UserManager::GAME_CTRL_STATUS_RUNNING;
					$sql = "UPDATE ut_game_ctrl SET status = ?, date_start = ?, date_end = ? WHERE id = 1";
					$param = array($gm_ctrls[$unit_no]['status'],$gm_ctrls[$unit_no]['date_start'],$gm_ctrls[$unit_no]['date_end']);
					$ret = $unit_m->executeForUnit($unit_no, $sql, $param);
					if (!$ret || Ethna::isError($ret)) {
						return 'admin_error_500';
					}
					$mes[] = "unit $unit_no maintenance finished (end time passed)";
				}
			}
		}

		//----------------------------------------------------------------------
		// メンテナンス開始ボタンが押下された場合
		//----------------------------------------------------------------------
		//全部NULLが送られてきたら無視する
		if (($null_chk != count($this->af->form))&&(is_null($btf) == true)) {
			$comment = "";
			if ($gm_ctrl['status'] == Pp_UserManager::GAME_CTRL_STATUS_RUNNING) {
				//稼働中(0)
				$gm_ctrl['date_start'] = $now;
				$gm_ctrl['date_end'] = $end;
				if ($act == Pp_UserManager::GAME_CTRL_STATUS_MAINTENANCE_BEFORE) {
					$gm_ctrl['date_start'] = $date_start;
					$gm_ctrl['date_end'] = $date_end;
					$gm_ctrl['status'] = Pp_UserManager::GAME_CTRL_STATUS_MAINTENANCE_BEFORE;
					$action = 'maintenance_before';
					$comment = "set maintenance before";
					//メンテ開始時間を過ぎていたら
					if ($gm_ctrl['date_start'] <= $now) {
						$gm_ctrl['status'] = Pp_UserManager::GAME_CTRL_STATUS_MAINTENANCE;
						$comment = "set maintenance start";
					}
					$update = true;
				}
			} else {
				//メンテナンス前(1)
				//メンテナンス中(2)
				if ($act == Pp_UserManager::GAME_CTRL_STATUS_RUNNING) {
					$gm_ctrl['status'] = Pp_UserManager::GAME_CTRL_STATUS_RUNNING;//メンテ中止
					$gm_ctrl['date_end'] = $now;
					$action = 'maintenance_stop';
				} else {
					$gm_ctrl['date_end'] = $date_end;//終了時刻更新
					$action = 'date_end_update';
					//メンテ終了時間を過ぎていたら
					if ($gm_ctrl['date_end'] <= $now) {
						$gm_ctrl['status'] = Pp_UserManager::GAME_CTRL_STATUS_RUNNING;
						$action = 'date_end_update_and_stop';
					}
				}
				$comment = "set maintenance stop";
				$update = true;
			}
			//更新
			if ($update) {
				$ret = $user_m->updGameCtrl($gm_ctrl);
				if (!$ret || Ethna::isError($ret)) {
					return 'admin_error_500';
				}
				$mes[] = "unit $unit $comment";
				// ログ
				//$log_columns = $columns;
				$log_columns = array();
				$log_columns['date_start'] = $gm_ctrl['date_start'];
				$log_columns['date_end'] = $gm_ctrl['date_end'];
				$admin_m->addAdminOperationLog('/announce/gamectrl', 'content_log',
					array_merge(array(
						'user'   => $this->session->get('lid'),
						'action' => $action,
					), $log_columns)
				);

				//他のユニットも同期させる場合
				if ($unitsync == 1) {
					$sql = "UPDATE ut_game_ctrl SET status = ?, btf = ?, date_start = ?, date_end = ? WHERE id = 1";
					$param = array($gm_ctrl['status'],$gm_ctrl['btf'],$gm_ctrl['date_start'],$gm_ctrl['date_end']);
					foreach ($unit_all as $unit_no => $unit_info) {
						if ($unit_no != $unit) {
							$ret = $unit_m->executeForUnit($unit_no, $sql, $param);
							if (!$ret || Ethna::isError($ret)) {
								return 'admin_error_500';
							}
							$mes[] = "unit $unit_no $comment";
							$gm_ctrls[$unit_no]['status'] = $gm_ctrl['status'];
							$gm_ctrls[$unit_no]['btf'] = $gm_ctrl['btf'];
							$gm_ctrls[$unit_no]['date_start'] = $gm_ctrl['date_start'];
							$gm_ctrls[$unit_no]['date_end'] = $gm_ctrl['date_end'];
						}
					}
				}
			}
		// 初期表示処理
		} else {
			//稼働中(0)
			if ($gm_ctrl['status'] == Pp_UserManager::GAME_CTRL_STATUS_RUNNING) {
				$gm_ctrl['date_start'] = $now;
				$gm_ctrl['date_end'] = $end;
			}
		}

		if (is_null($btf) == false) {
			//BTF変更
			$gm_ctrl['btf'] = $btf;
			$comment = "set btf:$btf";

			if ($btf == 0) {
				$action = 'btf_reset';
			} else {
				$action = 'btf_set';
			}
			$log_columns = array();
			$admin_m->addAdminOperationLog('/announce/gamectrl', 'content_log',
				array_merge(array(
					'user'   => $this->session->get('lid'),
					'action' => $action,
				), $log_columns)
			);

			$ret = $user_m->updGameCtrl($gm_ctrl);
			if (!$ret || Ethna::isError($ret)) {
				return 'admin_error_500';
			}
			$mes[] = "unit $unit $comment";

			if ($unitsync == 1) {
				//他ユニット同期
				$sql = "UPDATE ut_game_ctrl SET status = ?, btf = ?, date_start = ?, date_end = ? WHERE id = 1";
				$param = array($gm_ctrl['status'],$gm_ctrl['btf'],$gm_ctrl['date_start'],$gm_ctrl['date_end']);
				foreach ($unit_all as $unit_no => $unit_info) {
					if ($unit_no != $unit) {
						$ret = $unit_m->executeForUnit($unit_no, $sql, $param);
						if (!$ret || Ethna::isError($ret)) {
							return 'admin_error_500';
						}
						$mes[] = "unit $unit_no $comment";
						$gm_ctrls[$unit_no]['status'] = $gm_ctrl['status'];
						$gm_ctrls[$unit_no]['btf'] = $gm_ctrl['btf'];
						$gm_ctrls[$unit_no]['date_start'] = $gm_ctrl['date_start'];
						$gm_ctrls[$unit_no]['date_end'] = $gm_ctrl['date_end'];
					}
				}
			}
		}

		//各ユニットのステータス
		foreach ($unit_all as $unit_no => $unit_info) {
			if ($unit_no != $unit) {
				//$unit_stat[$unit_no] = $gm_ctrls[$unit_no]['status'];
				$unit_stat[$unit_no]['status'] = $gm_ctrls[$unit_no]['status'];
				$unit_stat[$unit_no]['btf'] = $gm_ctrls[$unit_no]['btf'];
			} else {
				//$unit_stat[$unit_no] = $gm_ctrl['status'];
				$unit_stat[$unit_no]['status'] = $gm_ctrl['status'];
				$unit_stat[$unit_no]['btf'] = $gm_ctrl['btf'];
			}
		}

		$this->af->setApp('status', $gm_ctrl['status']);
		$this->af->setApp('btf', $gm_ctrl['btf']);
		$this->af->setApp('date_start', $gm_ctrl['date_start']);
		$this->af->setApp('date_end', $gm_ctrl['date_end']);
		$this->af->setApp('date_now', $now);

		$this->af->setApp('mes', $mes);
		$this->af->setApp('unit_stat', $unit_stat);

		return 'admin_announce_gamectrl_index';
	}
}
