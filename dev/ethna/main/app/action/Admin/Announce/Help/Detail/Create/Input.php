<?php
/**
 *  Admin/Announce/Help/Detail/Create/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_help_detail_create_input Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHelpDetailCreateInput extends Pp_Form_AdminAnnounceHelpDetail
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'help_id' => array('required' => false),
		'category_id' => array('required' => false),
	);
}

/**
 *  admin_announce_help_detail_create_input action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHelpDetailCreateInput extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_help_detail_create_input Action.
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
	 *  admin_announce_help_detail_create_input action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$help_m =& $this->backend->getManager('AdminHelp');
		$help_id = $this->af->get('help_id');

		if ($help_id) {
			$row = $help_m->getHelpDetail($help_id);
			if (!$row) {
				return 'admin_error_500';
			}

			$this->af->setApp('row', $row);
			$this->af->setApp('priority', $row['priority']);
			$this->af->setApp('category_id', $row['category_id']);
		}

		return 'admin_announce_help_detail_create_input';
	}
}
