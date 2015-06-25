<?php
/**
 *  Resource/HelpDetail.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//////////////////////////
// APIのアクションの場合 //
//////////////////////////

/**
 *  resource_helpDetail Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ResourceHelpDetail extends Ethna_ActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'help_id',
	);
}

/**
 *  resource_helpDetail action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ResourceHelpDetail extends Ethna_ActionClass
{
	/**
	 *  preprocess of resource_helpDetail Action.
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
	 *  resource_helpDetail action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'resource_helpDetail';
	}
}
