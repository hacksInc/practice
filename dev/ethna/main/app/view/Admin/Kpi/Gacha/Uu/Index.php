<?php
/**
 *  Admin/Kpi/Gacha/Uu/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_gacha_uu_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiGachaUuIndex extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
        $this->af->setApp('create_file_path', '/admin/kpi/gacha/uu');

        $this->af->setApp('form_template', $this->af->form_template);
    }
}
