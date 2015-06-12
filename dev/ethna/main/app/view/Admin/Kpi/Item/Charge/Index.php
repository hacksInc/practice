<?php
/**
 *  Admin/Kpi/Item/Charge/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_item_charge_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiItemChargeIndex extends Pp_AdminViewClass
{

    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
        $this->af->setApp('create_file_path', '/admin/kpi/item/charge');
//        $this->af->setApp('dialog_url', '/admin/log/cs/friend/info');
//        $this->af->setApp('dialog_title', 'フレンド申請情報詳細');

        $this->af->setApp('form_template', $this->af->form_template);
    }
}
