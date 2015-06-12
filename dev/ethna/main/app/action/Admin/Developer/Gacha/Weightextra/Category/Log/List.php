<?php
/**
 *  Admin/Developer/Gacha/Weightextra/Category/Log/List.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'File/Find.php';
require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../../Pp_Form_AdminDeveloperGacha.php';

/**
 *  admin_developer_gacha_weightextra_category_log_list Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaWeightextraCategoryLogList extends Pp_Form_AdminDeveloperGacha
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'gacha_id',
    );
}

/**
 *  admin_developer_gacha_weightextra_category_log_list action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaWeightextraCategoryLogList extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_weightextra_category_log_list Action.
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
		
		$this->af->set('table', 'm_gacha_extra_category');
    }

    /**
     *  admin_developer_gacha_weightextra_category_log_list action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
//      return 'admin_developer_gacha_weightextra_category_log_list';
		
		$developer_m =& $this->backend->getManager('Developer');
//		$gacha_id = $this->session->get('gacha_id');
		$gacha_id = $this->af->get('gacha_id');
		
		$log_subdir = $developer_m->getGachaWeightExtraCategoryUploadLogSubdir($gacha_id);
		$dir = BASE . $log_subdir;
		
		$this->af->setApp('dir', $dir);
		$this->session->set('gacha_id', $gacha_id);
		
		return $this->performMasterLogList();
    }
}

?>