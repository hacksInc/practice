<?php
/**
 *  Admin/Announce/News/Content/Flag/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_announce_news_content_flag_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceNewsContentFlagExec extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'content_id',
		'test_flag',
    );
}

/**
 *  admin_announce_news_content_flag_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceNewsContentFlagExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_news_content_flag_exec Action.
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
     *  admin_announce_news_content_flag_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$news_m =& $this->backend->getManager('AdminNews');
		$admin_m =& $this->backend->getManager('Admin');

		$columns = array(
			'content_id' => $this->af->get('content_id'),
			'test_flag'  => $this->af->get('test_flag'),
		);

		$ret = $news_m->updateNewsContent($columns);
		if (!$ret || Ethna::isError($ret)) {
			return 'admin_error_500';
		}
		
		// ログ
		$admin_m->addAdminOperationLog('/announce/news', 'content_log', 
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $columns)
		);
		
        return 'admin_announce_news_content_flag_exec';
    }
}

?>