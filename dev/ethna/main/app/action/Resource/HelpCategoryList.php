<?php
/**
 *  Resource/HelpCategoryList.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//////////////////////////
// APIのアクションの場合 //
//////////////////////////

/**
 *  resource_helpCategoryList Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ResourceHelpCategoryList extends Ethna_ActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
	);
}

/**
 *  resource_helpCategoryList action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ResourceHelpCategoryList extends Ethna_ActionClass
{
	/**
	 *  preprocess of resource_helpCategoryList Action.
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
	 *  resource_helpCategoryList action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'resource_helpCategoryList';
	}
}
