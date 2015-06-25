<?php
/**
 *  Admin/Log/Cs/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_log_cs_download view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminLogCsDownload extends Pp_AdminViewClass
{
	protected $file_name     = null;
	protected $file          = null;
	protected $download_uniq = null;

	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$file_name = $this->af->get('file_name');

		if (preg_match('/item_log_data/', $file_name, $matches) === 1){
			$file = LOGDATA_PATH_ITEM_DATA . '/' . $file_name;
		} elseif (preg_match('/monster_log_data/', $file_name, $matches) === 1){
			$file = LOGDATA_PATH_MONSTER_DATA . '/' . $file_name;
		} elseif (preg_match('/present_log_data/', $file_name, $matches) === 1){
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
			} elseif (preg_match('/photo_log_data/', $file_name, $matches) === 1){
				$file = LOGDATA_PATH_PHOTO_DATA . '/' . $file_name;
			} elseif (preg_match('/friend_log_data/', $file_name, $matches) === 1){
				$file = LOGDATA_PATH_FRIEND_DATA . '/' . $file_name;
			} elseif (preg_match('/accounting_log_data/', $file_name, $matches) === 1){
				$file = LOGDATA_PATH_ACCOUNTING_DATA . '/' . $file_name;
			} elseif (preg_match('/character_log_data/', $file_name, $matches) === 1){
				$file = LOGDATA_PATH_CHARACTER_DATA . '/' . $file_name;
			} elseif (preg_match('/stress_log_data/', $file_name, $matches) === 1){
				$file = LOGDATA_PATH_STRESS_DATA . '/' . $file_name;
			} elseif (preg_match('/mission_log_data/', $file_name, $matches) === 1){
				$file = LOGDATA_PATH_MISSION_DATA . '/' . $file_name;
			} elseif (preg_match('/area_log_data/', $file_name, $matches) === 1){
				$file = LOGDATA_PATH_AREA_DATA . '/' . $file_name;
			} else {
				$file_name = $this->af->getApp('file_name');
				if (!$file_name) {
					exit();
				}

				$file = $this->backend->ctl->getDirectory('tmp') . '/' . $file_name;

				$download_uniq = $this->af->getApp('download_uniq');
				if ($download_uniq) {
					$this->download_uniq = $download_uniq;
				}
			}

		$this->file_name = $file_name;
		$this->file      = $file;
	}

	/**
	 * 
	 */
	function forward()
	{
		$download_uniq = $this->download_uniq;
		$file_name     = $this->file_name;
		$file          = $this->file;

		$mime_type = 'text/plain';

		if ($download_uniq) {
			setcookie('download_uniq', $download_uniq, $_SERVER['REQUEST_TIME'] + 86400, '/psychopass_game/admin/');
		}

		header("Content-type: $mime_type");
		header("Content-Disposition: attachment; filename=" . $file_name . ".csv");
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
