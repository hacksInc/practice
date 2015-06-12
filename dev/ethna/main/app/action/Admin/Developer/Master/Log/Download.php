<?php
/**
 *  Admin/Developer/Master/Log/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_master_log_download Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperMasterLogDownload extends Pp_AdminActionForm
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
 *  admin_developer_master_log_download action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperMasterLogDownload extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_master_log_download Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		$developer_m =& $this->backend->getManager('Developer');
		$file = $this->af->get('file');
		
		$fullpath = BASE . Pp_DeveloperManager::MASTER_UPLOAD_LOG_SUBDIR . '/' . $file;
		if (!is_file($fullpath)) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $fullpath);
			return 'admin_error_400';
		}
		
		$this->af->setApp('fullpath', $fullpath);

		return null;
    }

    /**
     *  admin_developer_master_log_download action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_master_log_download';
    }
}

?>