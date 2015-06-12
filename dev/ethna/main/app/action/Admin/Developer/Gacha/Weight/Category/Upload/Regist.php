<?php
/**
 *  Admin/Developer/Gacha/Weight/Category/Upload/Regist.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../../Pp_Form_AdminDeveloperGacha.php';

/**
 *  admin_developer_gacha_weight_category_upload_regist Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaWeightCategoryUploadRegist extends Pp_Form_AdminDeveloperGacha
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'file' => array(
			// フォームの定義
			'type'          => VAR_TYPE_STRING,    // 入力値型
			'form_type'     => FORM_TYPE_TEXT,  // フォーム型
			'name'          => 'ファイル位置',      // 表示名

			// バリデータ(記述順にバリデータが実行されます)
			'required'      => true,            // 必須オプション(true/false)
			'min'           => null,            // 最小値
			'max'           => null,            // 最大値
			'regexp'        => null,            // 文字種指定(正規表現)

			// フィルタ
			'filter'        => null,            // 入力値変換フィルタオプション
		),
		'crudlist' => array(
			// フォームの定義
			'type'          => array( VAR_TYPE_STRING ), // 入力値型
			'form_type'     => FORM_TYPE_TEXT,  // フォーム型
			'name'          => '更新種別',      // 表示名

			// バリデータ(記述順にバリデータが実行されます)
			'required'      => true,            // 必須オプション(true/false)
			'min'           => null,            // 最小値
			'max'           => null,            // 最大値
			'regexp'        => null,            // 文字種指定(正規表現)

			// フィルタ
			'filter'        => null,            // 入力値変換フィルタオプション
		),
		
//		'gacha_id',
    );
}

/**
 *  admin_developer_gacha_weight_category_upload_regist action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaWeightCategoryUploadRegist extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_weight_category_upload_regist Action.
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
 
		$developer_m =& $this->backend->getManager('Developer');
		
//		$gacha_id = $this->af->get('gacha_id');
		$gacha_id = $this->session->get('gacha_id');
		if (!$gacha_id) {
			return 'admin_error_400';
		}
		
		$conditions = array(0 => $gacha_id);
		$this->af->setApp('conditions', $conditions);
		
//		$log_subdir = Pp_DeveloperManager::MASTER_UPLOAD_LOG_SUBDIR . '/'
//		            . 'm_gacha_category/' . $gacha_id;
		$log_subdir = $developer_m->getGachaWeightCategoryUploadLogSubdir($gacha_id);
		$this->af->setApp('log_subdir', $log_subdir);
		
		$this->af->set('table', 'm_gacha_category');
		
		$this->af->setApp('back_location', '../../index?gacha_id=' . $gacha_id);
    }
	
    /**
     *  admin_developer_gacha_weight_category_upload_regist action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
//      return 'admin_developer_gacha_weight_category_upload_regist';
		return $this->performMasterUploadRegist();
    }
}

?>