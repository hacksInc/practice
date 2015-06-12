<?php
/**
 *  Admin/Developer/Gacha/Weightextra/Item/Log/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../../Pp_Form_AdminDeveloperGacha.php';

/**
 *  admin_developer_gacha_weightextra_item_log_download Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaWeightextraItemLogDownload extends Pp_Form_AdminDeveloperGacha
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'file' => array(
            'type'        => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => true,                 // Required Option(true/false)
            'min'         => null,                 // Minimum value
            'max'         => 256,                  // Maximum value
            'regexp'      => '/^[a-z0-9_-]+\.csv$/', // String by Regexp
        ),
    );
}

/**
 *  admin_developer_gacha_weightextra_item_log_download action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaWeightextraItemLogDownload extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_weightextra_item_log_download Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
		
		$developer_m =& $this->backend->getManager('Developer');
		$file = $this->af->get('file');
		$gacha_id = $this->session->get('gacha_id');
		if (!$gacha_id) {
			return 'admin_error_400';
		}

		$log_subdir = $developer_m->getGachaWeightExtraItemUploadLogSubdir($gacha_id);
		$fullpath = BASE . $log_subdir . '/' . $file;
		if (!is_file($fullpath)) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $fullpath);
			return 'admin_error_400';
		}
		
		$this->af->setApp('fullpath', $fullpath);

		return null;
    }

    /**
     *  admin_developer_gacha_weightextra_item_log_download action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
//      return 'admin_developer_gacha_weightextra_item_log_download';
        return 'admin_developer_master_log_download';
    }
}

?>