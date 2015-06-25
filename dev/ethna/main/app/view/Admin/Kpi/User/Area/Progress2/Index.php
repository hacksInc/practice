<?php
/**
 *  Admin/Kpi/User/Area/Progress2/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_user_area_progress2_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiUserAreaProgress2Index extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
        $this->af->setApp('create_file_path', '/admin/kpi/user/area/progress2');

        $this->af->setApp('form_template', $this->af->form_template);
    }
}
