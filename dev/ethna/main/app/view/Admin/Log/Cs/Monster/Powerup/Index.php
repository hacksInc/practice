<?php
/**
 *  Admin/Log/Cs/Monster/Powerup/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_monster_powerup_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsMonsterPowerupIndex extends Pp_AdminViewClass
{

	/**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {

        $this->af->setApp('create_file_path', '/admin/log/cs/monster/powerup');
        $this->af->setApp('dialog_url', '/admin/log/cs/monster/powerup/info');
        $this->af->setApp('dialog_title', 'モンスター強化合成情報詳細');

        $this->af->setApp('form_template', $this->af->form_template);
    }
}
