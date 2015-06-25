<?php
/**
 *  Admin/Developer/Assetbundle/Version/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_assetbundle_version_update_input Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleVersionUpdateInput extends Pp_Form_AdminDeveloperAssetbundleVersion
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
 *  admin_developer_assetbundle_version_update_input action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleVersionUpdateInput extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_assetbundle_version_update_input Action.
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
		
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');
		$date_start = $this->af->get('date_start');
		
		$row = $assetbundle_m->getVersion($date_start);
		if (!$row || Ethna::isError($row)) {
			return 'admin_error_500';
		}
		
		$this->af->setApp('row', $row);
    }

    /**
     *  admin_developer_assetbundle_version_update_input action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_assetbundle_version_update_input';
    }
}

?>