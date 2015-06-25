<?php
/**
 *  Admin/Program/Deploy/Diff/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_program_deploy_diff_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminProgramDeployDiffIndex extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  admin_program_deploy_diff_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminProgramDeployDiffIndex extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_program_deploy_diff_index Action.
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
     *  admin_program_deploy_diff_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_program_deploy_diff_index';
    }
}

?>