<?php
/**
 *  Resource/News.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_ResourceActionClass.php';

/**
 *  resource_news Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ResourceNews extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'lang' => array(
            // Form definition
            'type'     => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required' => true,            // Required Option(true/false)
            'regexp'   => '/^ja$|^en$|^es$/', // String by Regexp
        ),
        'ua' => array(
            // Form definition
            'type'     => VAR_TYPE_INT, // Input type
        
            //  Validator (executes Validator by written order.)
            'required' => true,            // Required Option(true/false)
			'min'      => 1,
			'max'      => 2,
        ),
    );
}

/**
 *  resource_news action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ResourceNews extends Pp_ResourceActionClass
{
    /**
     *  preprocess of resource_news Action.
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
    }

    /**
     *  resource_news action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'resource_news';
    }
}

?>