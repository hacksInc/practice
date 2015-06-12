<?php
/**
 *  Admin/Log/Cs/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Index.php';

/**
 *  admin_log_cs_download Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsDownload extends Pp_Form_AdminLogCs
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form_template = array(
        'file_name' => array(
            // Form definition
            'type'        => VAR_TYPE_STRING,     // Input type
            'form_type'   => FORM_TYPE_HIDDEN,   // Form type
            'name'        => 'ファイル名', // Display name

            //  Validator (executes Validator by written order.)
            'required'    => false,             // Required Option(true/false)
            //'min'         => 30000,            // Minimum value
            //'max'         => 40000,            // Maximum value
        ),
    );
    var $form = array(
        'file_name',
    );

    /**
     *  Form input value convert filter : sample
     *
     *  @access protected
     *  @param  mixed   $value  Form Input Value
     *  @return mixed           Converted result.
     */
    /*
    function _filter_sample($value)
    {
        //  convert to upper case.
        return strtoupper($value);
    }
    */
}

/**
 *  admin_log_cs_download action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsDownload extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_log_cs_download Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */

    function prepare()
    {

        // アクセス制御
        if ($this->must_login && $this->must_permission) {
            $ret = $this->permit();
            if ($ret) {
                return $ret;
            }
        }

        if ($this->af->validate() > 0) {
            return 'admin_log_cs_item_index';
        }

    }

    /**
     *  admin_log_cs_download action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_log_cs_download';
    }
}
