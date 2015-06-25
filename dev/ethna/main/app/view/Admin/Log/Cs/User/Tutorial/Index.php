<?php
/**
 *  Admin/Log/Cs/User/Tutorial/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_user_tutorial_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsUserTutorialIndex extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
        $this->af->setApp('form_template', $this->af->form_template);
    }
}