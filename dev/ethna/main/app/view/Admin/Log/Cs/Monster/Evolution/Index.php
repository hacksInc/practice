<?php
/**
 *  Admin/Log/Cs/Monster/Evolution/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_monster_evolution_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsMonsterEvolutionIndex extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {

        $this->af->setApp('create_file_path', '/admin/log/cs/monster/evolution');
        $this->af->setApp('dialog_url', '/admin/log/cs/monster/evolution/info');
        $this->af->setApp('dialog_title', 'モンスター進化合成情報詳細');

        $this->af->setApp('form_template', $this->af->form_template);
    }
}
