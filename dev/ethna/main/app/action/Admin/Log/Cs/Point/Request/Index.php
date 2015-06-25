<?php
/**
 *  Admin/Log/Cs/Point/Request/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once 'Pp_LogdataViewPointManager.php';
require_once dirname(__FILE__) . '/../../Index.php';

/** admin_log_cs_point_request_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsPointRequest extends Pp_Form_AdminLogCs
{
    /**
     * コンストラクタ
     */
    function __construct(&$backend) {
        $form_template = array(
            'search_type' => array(
                // Form definition
                'type'        => VAR_TYPE_INT,     // Input type
                'form_type'   => FORM_TYPE_HIDDEN, // Form type
                'name'        => '検索種別',       // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,            // Required Option(true/false)
                'min'         => Pp_LogdataViewPointManager::SEARCH_TYPE_USER_ID, // Minimum value
                'max'         => Pp_LogdataViewPointManager::SEARCH_TYPE_STS_NG,  // Maximum value
            ),
        );

        foreach ($form_template as $key => $value) {
            $this->form_template[$key] = $value;
        }
		
        parent::__construct($backend);
    }
	
    /**
     *  ユーザ定義検証メソッド(フォーム値間の連携チェック等)
     *
     *  @access protected
     */
    function _validatePlus()
    {
		$search_type = $this->get('search_type');
		if ($search_type == Pp_LogdataViewPointManager::SEARCH_TYPE_USER_ID) {
			$search_user_id = $this->get('search_user_id');
			if (!$search_user_id) {
				$this->ae->add(null, 'ユーザーIDを入力して下さい', E_FORM_INVALIDVALUE);
			}
		}
    }
	
	/**
	 * 検索条件のフォームパラメータをまとめて取得する
	 */
	function getSearchParams()
	{
        $search_params = array(
            'date_from' => $this->get('search_date_from'),
            'date_to' => $this->get('search_date_to'),
        );
		
		$search_type = $this->get('search_type');
		if ($search_type == Pp_LogdataViewPointManager::SEARCH_TYPE_USER_ID) {
			$search_params['user_id'] = $this->get('search_user_id');
		} else if ($search_type == Pp_LogdataViewPointManager::SEARCH_TYPE_STS_NG) {
			$search_params['sts'] = 'NG';
		}
		
		return $search_params;
	}
}

/**
 *  admin_log_cs_point_request_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsPointRequestIndex extends Pp_Form_AdminLogCsPointRequest
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'search_date_from',
        'search_date_to',
        'search_user_id',
    );
}

/**
 *  admin_log_cs_point_request_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsPointRequestIndex extends Pp_Action_AdminLogCsIndex
{
    /**
     *  preprocess of admin_log_cs_point_request_index Action.
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
		
        return null;
    }

    /**
     *  admin_log_cs_point_request_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		return 'admin_log_cs_point_request_index';
    }
}
