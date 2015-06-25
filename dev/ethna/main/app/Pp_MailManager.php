<?php
/**
 *  Pp_MailManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_MailManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_MailManager extends Ethna_AppManager
{
	/**
	 * メール一覧を取得する
	 * 
	 * @param type $user_id
	 * @param type $last_user_mail_id 前回取得済みのメールユニークID最大値
	 * @return bool 成否
	 */
/*
	function getUserMailList($user_id, $last_user_mail_id = null)
	{
		$user_m =& $this->backend->getManager('User');

		if ($last_user_mail_id === null) {
			$user_base = $user_m->getUserBase($user_id);
			if (!$user_base || Ethna::isError($user_base)) {
				return false;
			}
			
			$last_user_mail_id = $user_base['last_user_mail_id'];
		}
		
		$param = array($user_id, $last_user_mail_id);
		$sql = "SELECT user_mail_id, mail_subject, mail_body, mail_from, date_send"
			 . " FROM t_user_mail"
			 . " WHERE user_id = ?"
			 . " AND user_mail_id > ?"
			 . " ORDER BY user_mail_id";//TODO 件数制限は？
		$list = $this->db->GetAll($sql, $param);
		
		if (is_array($list)) {
			$cnt = count($list);
			if ($cnt > 0) {
				$columns = array('user_mail_id' => $list[$cnt - 1]['user_mail_id']);
				$ret = $user_m->setUserBase($user_id, $columns);
				if (!$ret || Ethna::isError($ret)) {
					return false;
				}
			}
		}
		
		return $list;
	}
*/
}
?>
