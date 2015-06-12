<?php
/**
 *  Admin/Developer/Assetbundle/Version/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminAssetbundleManager.php';
require_once 'Pp_AdminActionClass.php';

/** admin_developer_assetbundle_version_* で共通のアクションフォーム定義 */
class Pp_Form_AdminDeveloperAssetbundleVersion extends Pp_AdminActionForm
{
	var $form_template = array(
        'app_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//瀬良さんから要望があったので末尾の"バージョン"を非表示にする。(2013/7/16)
//            'name'        => 'アプリバージョン', // Display name
            'name'        => 'アプリ', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),

        'res_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//            'name'        => 'リソースバージョン', // Display name
            'name'        => 'リソース', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),

        'mon_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//            'name'        => 'モンスターデータバージョン', // Display name
            'name'        => 'モンスターデータ', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),

        'mon_image_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//            'name'        => 'モンスターイメージバージョン', // Display name
            'name'        => 'モンスターイメージ', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),

        'skilldata_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//            'name'        => 'スキルデータバージョン', // Display name
            'name'        => 'スキルデータ', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),

        'skilleffect_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//            'name'        => 'スキルエフェクトバージョン', // Display name
            'name'        => 'スキルエフェクト', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),

        'bgmodel_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//            'name'        => '背景モデルのバージョン', // Display name
            'name'        => '背景モデル', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),

        'sound_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//            'name'        => 'サウンドのバージョン', // Display name
            'name'        => 'サウンド', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),

        'map_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//            'name'        => 'ソーシャルマップのバージョン', // Display name
            'name'        => 'ソーシャルマップ', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),

        'worldmap_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//            'name'        => 'ワールドマップのバージョン', // Display name
            'name'        => 'ワールドマップ', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),

        'mon_exp_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//            'name'        => 'モンスター経験値テーブルのバージョン', // Display name
            'name'        => 'モンスター経験値テーブル', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),

        'player_rank_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//            'name'        => 'プレイヤーランクテーブルのバージョン', // Display name
            'name'        => 'プレイヤーランクテーブル', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),

        'ach_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
//            'name'        => '勲章データのバージョン', // Display name
            'name'        => '勲章データ', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
		  
        'mon_act_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'モンスターアクションデータ', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
		  
        'boost_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => '特攻', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
		  
        'badge_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'バッジ', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
		  
        'badge_material_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'バッジ素材', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
		  
        'badge_skill_ver' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'バッジスキル', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
		  
        'clear' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,     // Input type
            'form_type'   => FORM_TYPE_SELECT, // Form type
            'name'        => 'キャッシュクリアフラグ', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 0,               // Minimum value
            'max'         => 1,               // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
		
		'date_start' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_TEXT,   // Form type
			'name'        => 'リリース開始日時', // Display name

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
			//  Filter
			'filter'      => 'space_zentohan,numeric_zentohan,ltrim,rtrim', // Optional Input filter to convert input
			'custom'      => 'checkDatetime9999', // Optional method name which
											      // is defined in this(parent) class.
		),
	);
	
	/**
	 * リソースバージョンマスタのリソース関連のフォーム定義を初期化する
	 */
	protected function initializeResVerFormDef()
	{
		if (isset($this->form[0]) || empty($this->form)) { // 配列の場合
			foreach (Pp_AdminAssetbundleManager::getResVerKeys() as $key) {
				if (!in_array($key, $this->form)) {
					$this->form[] = $key;
				}
			}
		} else { // 連想配列の場合
			foreach (Pp_AdminAssetbundleManager::getResVerKeys() as $key) {
				if (!isset($this->form[$key])) {
					$this->form[$key] = $this->form_template[$key];
				}
			}
		}
	}
}

/**
 *  admin_developer_assetbundle_version_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleVersionIndex extends Pp_Form_AdminDeveloperAssetbundleVersion
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  admin_developer_assetbundle_version_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleVersionIndex extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_assetbundle_version_index Action.
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
     *  admin_developer_assetbundle_version_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		return 'admin_developer_assetbundle_version_index';
    }
}

?>