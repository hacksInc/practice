<?php
/**
 *  Admin/Developer/Master/Upload/Regist.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_master_upload_regist Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperMasterUploadRegist extends Pp_AdminActionForm
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
        'table' => array(
            'required'    => true,                // Required Option(true/false)
        ),
    );

    /**
     *  Form input value convert filter : sample
     *
     *  @access protected
     *  @param  mixed   $value  Form Input Value
     *  @return mixed           Converted result.
     */
    /*
    function _filter_sample($value)
    {
        //  convert to upper case.
        return strtoupper($value);
    }
    */
}

/**
 *  admin_developer_master_upload_regist action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperMasterUploadRegist extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_master_upload_regist Action.
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
     *  admin_developer_master_upload_regist action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		return $this->performMasterUploadRegist();
    }
}

?>