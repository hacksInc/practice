<?php
/**
 *  Admin/Log/Cs/Friend/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_friend_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsFriendIndex extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
        $this->af->setApp('create_file_path', '/admin/log/cs/friend');
        $this->af->setApp('dialog_url', '/admin/log/cs/friend/info');
        $this->af->setApp('dialog_title', 'フレンド申請情報詳細');

        $this->af->setApp('form_template', $this->af->form_template);
    }
}
