<?php
/**
 *  Resource/Newscontentimage.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_ActionClass.php';

/**
 *  resource_image Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ResourceNewscontentimage extends Ethna_ActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'img_id',
		'type',
	);
}

/**
 *  resource_image action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ResourceNewscontentimage extends Ethna_ActionClass
{
	/**
	 *  preprocess of resource_image Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
/*
	function prepare()
	{
		// アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
	}
 */

	/**
	 *  resource_newscontentimage action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'resource_newscontentimage';
	}
}
