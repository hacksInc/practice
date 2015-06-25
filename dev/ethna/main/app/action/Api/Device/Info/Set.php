<?php
/**
 *  Api/Device/Info/Set.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  api_device_info_set Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiDeviceInfoSet extends Pp_ApiActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'device_info',
    );
}

/**
 *  api_device_info_set action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiDeviceInfoSet extends Pp_ApiActionClass
{
    /**
     *  preprocess of api_device_info_set Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        if ($this->af->validate() > 0) {
            return 'error_400';
        }

        return null;
    }

    /**
     *  api_device_info_set action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$this->performDeviceInfo();
		
        return 'api_json_encrypt';
    }
}

?>