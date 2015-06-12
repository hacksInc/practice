<?php
/**
 *  Resource/HelpDetailList.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//////////////////////////
// APIのアクションの場合 //
//////////////////////////

/**
 *  resource_helpDetailList view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ResourceHelpDetailList extends Pp_ViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$help_m =& $this->backend->getManager('Help');

		$category_id = $this->af->get('category_id');

		$list = $help_m->getHelpDetailList($category_id);

		$this->af->setApp('list', $list);
		$this->af->setAppNe('list', $list);
	}
}

