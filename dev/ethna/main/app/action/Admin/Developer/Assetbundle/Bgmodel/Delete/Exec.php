<?php
/**
 *  Admin/Developer/Assetbundle/Bgmodel/Delete/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_assetbundle_bgmodel_delete_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleBgmodelDeleteExec extends Pp_Form_AdminDeveloperAssetbundleBgmodel
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'id',
    );
}

/**
 *  admin_developer_assetbundle_bgmodel_delete_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleBgmodelDeleteExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_assetbundle_bgmodel_delete_exec Action.
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
     *  admin_developer_assetbundle_bgmodel_delete_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		$admin_m =& $this->backend->getManager('Admin');

		$id = $this->af->get('id');

		$row = $assetbundle_m->getMasterAssetBundle($id);
		if (!$row || Ethna::isError($row)) {
			return 'admin_error_500';
		}
		
		$ret = $assetbundle_m->deleteMasterAssetBundle($id);
		if (!$ret || Ethna::isError($ret)) {
			return 'admin_error_500';
		}
		
		// ログ
		$admin_m->addAdminOperationLog('/developer/assetbundle', 'bgmodel_log', 
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $row)
		);

		return 'admin_developer_assetbundle_bgmodel_delete_exec';
    }
}

?>