<?php
/**
 *  Admin/Announce/Home/Banner/End/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_home_banner_end_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHomeBannerEndExec extends Pp_Form_AdminAnnounceHomeBanner
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'hbanner_id',
    );
}

/**
 *  admin_announce_home_banner_end_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHomeBannerEndExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_home_banner_end_exec Action.
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
     *  admin_announce_home_banner_end_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$news_m =& $this->backend->getManager('AdminNews');
		$admin_m =& $this->backend->getManager('Admin');

		$columns = array(
			'hbanner_id' => $this->af->get('hbanner_id'),
			'date_end' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
			'account_upd' => $this->session->get('lid'),
		);

		$ret = $news_m->updateHomeBanner($columns);
		if (!$ret || Ethna::isError($ret)) {
			return 'admin_error_500';
		}
		
		// ログ
		$admin_m->addAdminOperationLog('/announce/home', 'banner_log', 
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $columns)
		);
		
        return 'admin_announce_home_banner_end_exec';
    }
}

?>