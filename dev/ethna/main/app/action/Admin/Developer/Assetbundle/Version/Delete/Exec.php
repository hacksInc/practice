<?php
/**
 *  Admin/Developer/Assetbundle/Version/Delete/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_assetbundle_version_delete_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleVersionDeleteExec extends Pp_Form_AdminDeveloperAssetbundleVersion
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'date_start',
    );
}

/**
 *  admin_developer_assetbundle_version_delete_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleVersionDeleteExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_assetbundle_version_delete_exec Action.
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
     *  admin_developer_assetbundle_version_delete_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		$admin_m =& $this->backend->getManager('Admin');

		$date_start = $this->af->get('date_start');

		$row = $assetbundle_m->getVersion($date_start);
		if (!$row || Ethna::isError($row)) {
			return 'admin_error_500';
		}
		
		$ret = $assetbundle_m->deleteVersion($date_start);
		if (!$ret || Ethna::isError($ret)) {
			return 'admin_error_500';
		}
		
		// ログ
		$admin_m->addAdminOperationLog('/developer/assetbundle', 'version_log', 
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $row)
		);

        return 'admin_developer_assetbundle_version_delete_exec';
    }
}

?>