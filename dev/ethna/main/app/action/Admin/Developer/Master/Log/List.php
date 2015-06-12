<?php
/**
 *  Admin/Developer/Master/Log/List.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'File/Find.php';
require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_master_log_list Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperMasterLogList extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'table' => array(
            'required'    => true, 
        ),
    );
}

/**
 *  admin_developer_master_log_list action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperMasterLogList extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_master_log_list Action.
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
     *  admin_developer_master_log_list action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		return $this->performMasterLogList();
    }
}

?>