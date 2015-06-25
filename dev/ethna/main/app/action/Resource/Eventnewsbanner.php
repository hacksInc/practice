<?php
/**
 *  Resource/Eventnewsbanner.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_ResourceActionClass.php';

/**
 *  resource_eventnewsbanner Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ResourceEventnewsbanner extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'content_id' => array(
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
 *  resource_eventnewsbanner action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ResourceEventnewsbanner extends Pp_ResourceActionClass
{
    /**
     *  preprocess of resource_eventnewsbanner Action.
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

		$news_m =& $this->backend->getManager('News');
		$content_id = $this->af->get('content_id');
		$is_available = $news_m->isEventnewsbannerContentIdAvailable($content_id);
		if (!$is_available) {
			header('HTTP/1.0 400 Bad Request');
			exit;
		}
    }

    /**
     *  resource_eventnewsbanner action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'resource_eventnewsbanner';
    }
}

?>