<?php
/**
 *  Admin/Kpi/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_kpi_download Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiDownload extends Pp_AdminActionForm
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
}

/**
 *  admin_kpi_download action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminKpiDownload extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_kpi_download Action.
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
            return 'admin_kpi_index';
        }

    }

    /**
     *  admin_kpi_download action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {

        return 'admin_kpi_download';
    }
}
