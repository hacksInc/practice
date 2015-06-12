<?php
/**
 *  Admin/Log/Cs/Quest/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_item_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsQuestIndex extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {

        $this->af->setApp('create_file_path', '/admin/log/cs/quest');
        $this->af->setApp('dialog_url', '/admin/log/cs/quest/info');
        $this->af->setApp('dialog_title', 'クエスト情報詳細');

        $this->af->setApp('form_template', $this->af->form_template);
    }
}
