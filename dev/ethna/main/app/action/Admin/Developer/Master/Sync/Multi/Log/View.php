<?php
/**
 *  Admin/Developer/Master/Sync/Multi/Log/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_master_sync_multi_log_view Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperMasterSyncMultiLogView extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'mode' => array(
            'type'        => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => true,                 // Required Option(true/false)
            'min'         => null,                 // Minimum value
            'max'         => 16,                   // Maximum value
            'regexp'      => '/^deploy|standby$/', // String by Regexp
        ),
    );
}

/**
 *  admin_developer_master_sync_multi_log_view action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperMasterSyncMultiLogView extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_master_sync_multi_log_view Action.
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
     *  admin_developer_master_sync_multi_log_view action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_master_sync_multi_log_view';
    }
}

?>