<?php
/**
 *  Resource/Homebanner.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_ResourceActionClass.php';

/**
 *  resource_homebanner Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ResourceHomebanner extends Pp_ActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'img_id' => array(
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
 *  resource_homebanner action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ResourceHomebanner extends Pp_ResourceActionClass
{
	/**
	 *  preprocess of resource_homebanner Action.
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
		$img_id = $this->af->get('img_id');
		$is_available = $news_m->isHomebannerImgIdAvailable($img_id);
		if (!$is_available) {
			header('HTTP/1.0 400 Bad Request');
			exit;
		}
	}

	/**
	 *  resource_homebanner action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'resource_homebanner';
	}
}

?>
