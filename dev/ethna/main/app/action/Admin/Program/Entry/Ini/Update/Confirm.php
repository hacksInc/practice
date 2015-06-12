<?php
/**
 *  Admin/Program/Entry/Ini/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../Pp_Form_AdminProgramEntry.php';
require_once dirname(__FILE__) . '/../../Pp_Action_AdminProgramEntry.php';

/**
 *  admin_program_entry_ini_update_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminProgramEntryIniUpdateConfirm extends Pp_Form_AdminProgramEntry
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'current_ver',
		'review_ver',
    );
}

/**
 *  admin_program_entry_ini_update_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminProgramEntryIniUpdateConfirm extends Pp_Action_AdminProgramEntry
{
    /**
     *  preprocess of admin_program_entry_ini_update_confirm Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
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

    /**
     *  admin_program_entry_ini_update_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_program_entry_ini_update_confirm';
    }
}

?>