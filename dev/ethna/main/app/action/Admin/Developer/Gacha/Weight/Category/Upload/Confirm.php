<?php
/**
 *  Admin/Developer/Gacha/Weight/Category/Upload/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../../Pp_Form_AdminDeveloperGacha.php';

/**
 *  admin_developer_gacha_weight_category_upload_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaWeightCategoryUploadConfirm extends Pp_Form_AdminDeveloperGacha
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'xml' => array(
			// フォームの定義
			'type'          => VAR_TYPE_FILE,    // 入力値型
			'name'          => 'XMLファイル',      // 表示名

			// バリデータ(記述順にバリデータが実行されます)
			'required'      => true,            // 必須オプション(true/false)
		),
//        'table' => array(
//            'required'    => true,                // Required Option(true/false)
//        ),
		
		'gacha_id',
    );
}

/**
 *  admin_developer_gacha_weight_category_upload_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaWeightCategoryUploadConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_weight_category_upload_confirm Action.
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
		
		$gacha_id = $this->af->get('gacha_id');

		$conditions = array(0 => $gacha_id);
		$this->af->setApp('conditions', $conditions);
		
		$this->af->set('table', 'm_gacha_category');
		
		$this->session->set('gacha_id', $gacha_id);
    }

    /**
     *  admin_developer_gacha_weight_category_upload_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
//      return 'admin_developer_gacha_weight_category_upload_confirm';
		return $this->performMasterUploadConfirm();
    }
}

?>