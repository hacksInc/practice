<?php
/**
 *  Resource/Gachabanner.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_ResourceActionClass.php';

/**
 *  resource_gachabanner Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ResourceGachabanner extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'gacha_id' => array(
            // Form definition
            'type'     => VAR_TYPE_INT, // Input type
        
            //  Validator (executes Validator by written order.)
            'required' => true,            // Required Option(true/false)
			'min'      => 1,
			'max'      => null,
        ),
    );
}

/**
 *  resource_gachabanner action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ResourceGachabanner extends Pp_ResourceActionClass
{
    /**
     *  preprocess of resource_gachabanner Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        if ($this->af->validate() > 0) {
			header('HTTP/1.0 400 Bad Request');
			exit;
        }

		$shop_m =& $this->backend->getManager('Shop');
		$gacha_id = $this->af->get('gacha_id');
		$is_available = $shop_m->isGachabannerGachaIdAvailable($gacha_id);
		if (!$is_available) {
			header('HTTP/1.0 400 Bad Request');
			exit;
		}
    }

    /**
     *  resource_gachabanner action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'resource_gachabanner';
    }
}

?>