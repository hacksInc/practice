<?php
/**
 *  Admin/Kpi/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_kpi_download view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminKpiDownload extends Pp_AdminViewClass
{

	/**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
        $file_name = $this->af->get('file_name');
        $mime_type = 'text/plain';

        header("Content-type: $mime_type");
        header("Content-Disposition: attachment; filename=" . $file_name . ".csv");

        if (preg_match('/kpi_user_continuance_rate/', $file_name, $matches) === 1){
            $file = KPIDATA_PATH_USER_DATA . '/' . $file_name;
        } elseif (preg_match('/kpi_user_battle_progress/', $file_name, $matches) === 1){
            $file = KPIDATA_PATH_USER_DATA . '/' . $file_name;
        } elseif (preg_match('/kpi_item_charge_rate/', $file_name, $matches) === 1){
            $file = KPIDATA_PATH_ITEM_DATA . '/' . $file_name;
        } elseif (preg_match('/kpi_gacha_charge_rate/', $file_name, $matches) === 1){
            $file = KPIDATA_PATH_ITEM_DATA . '/' . $file_name;
        } elseif (preg_match('/kpi_gacha_uu_rate/', $file_name, $matches) === 1){
            $file = KPIDATA_PATH_ITEM_DATA . '/' . $file_name;
        } elseif (preg_match('/kpi_monster_use/', $file_name, $matches) === 1){
            $file = KPIDATA_PATH_MONSTER_DATA . '/' . $file_name;
/*        } elseif (preg_match('/present_log_data/', $file_name, $matches) === 1){
            $file = LOGDATA_PATH_PRESENT_DATA . '/' . $file_name;
        } elseif (preg_match('/quest_log_data/', $file_name, $matches) === 1){
            $file = LOGDATA_PATH_QUEST_DATA . '/' . $file_name;
        } elseif (preg_match('/achievement_log_data/', $file_name, $matches) === 1){
            $file = LOGDATA_PATH_ACHIEVEMENT_DATA . '/' . $file_name;
        } elseif ((preg_match('/user_log_data/', $file_name, $matches) === 1)
            || (preg_match('/user_login_log_data/', $file_name, $matches) === 1 )
            || (preg_match('/user_tutorial_log_data/', $file_name, $matches) === 1 )){
            $file = LOGDATA_PATH_USER_DATA . '/' . $file_name;
        } elseif (preg_match('/gacha_log_data/', $file_name, $matches) === 1){
            $file = LOGDATA_PATH_GACHA_DATA . '/' . $file_name;
        } elseif (preg_match('/friend_log_data/', $file_name, $matches) === 1){
            $file = LOGDATA_PATH_FRIEND_DATA . '/' . $file_name;*/
        } else {
            exit();
        }

        header('Content-Length: ' . filesize($file));
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        flush();
        readfile($file);
        unlink($file);
    }
}
