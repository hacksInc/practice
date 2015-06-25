<?php
/**
 *  Admin/Developer/Assetbundle/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_developer_assetbundle_* で共通のアクションフォーム定義 */
class Pp_Form_AdminDeveloperAssetbundle extends Pp_AdminActionForm
{
	var $form_template = array(
        'id' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => '管理ID',        // Display name
        
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

        'monster_icon' => array(
            // Form definition
            'type'        => VAR_TYPE_FILE,   // Input type
            'form_type'   => FORM_TYPE_FILE,  // Form type
            'name'        => 'モンスターアイコン', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => false,           // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
		
        'monster_image' => array(
            // Form definition
            'type'        => VAR_TYPE_FILE,   // Input type
            'form_type'   => FORM_TYPE_FILE,  // Form type
            'name'        => 'モンスター画像', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => false,           // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
		
        'asset_bundle_android' => array(
            // Form definition
            'type'        => VAR_TYPE_FILE,   // Input type
            'form_type'   => FORM_TYPE_FILE,  // Form type
            'name'        => 'アセットバンドルAndroid用', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => false,           // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => 'checkAssetbundleFile', // Optional method name which
                                                     // is defined in this(parent) class.
        ),
		
        'asset_bundle_iphone' => array(
            // Form definition
            'type'        => VAR_TYPE_FILE,   // Input type
            'form_type'   => FORM_TYPE_FILE,  // Form type
            'name'        => 'アセットバンドルiPhone用', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => false,           // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => 'checkAssetbundleFile', // Optional method name which
                                                     // is defined in this(parent) class.
        ),
		
        'asset_bundle_pc' => array(
            // Form definition
            'type'        => VAR_TYPE_FILE,   // Input type
            'form_type'   => FORM_TYPE_FILE,  // Form type
            'name'        => 'アセットバンドルPC用', // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => false,           // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => 'checkAssetbundleFile', // Optional method name which
                                                     // is defined in this(parent) class.
        ),
		
		'start_date' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_TEXT,   // Form type
			'name'        => '開始日',         // Display name

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
		
		'end_date' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_TEXT,   // Form type
			'name'        => '終了日',         // Display name

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

        'active_flg' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,     // Input type
            'form_type'   => FORM_TYPE_SELECT, // Form type
            'name'        => '活性フラグ',      // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
			'option'      => array(
				 0 => '0',
				 1 => '1',
			),
        ),
		
        'confirm_uniq' => array(
            // Form definition
            'type'        => VAR_TYPE_STRING,  // Input type
            'form_type'   => FORM_TYPE_HIDDEN, // Form type
			'name'        => 'confirm_uniq',   // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 1,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
	);

	/**
	 * input要素からファイル名を取得する
	 * 
	 * @param string $name input type="file"用のアクションフォーム変数名
	 * @return string ファイル名
	 */
	function getFileName($name)
	{
		$file = $this->get($name);
		if ($file) {
			return $file['name'];
		}
	}
	
	/**
	 * input要素からファイル内容を取得する
	 * 
	 * @param string $name input type="file"用のアクションフォーム変数名
	 * @return string ファイル名
	 */
	function getFileContents($name)
	{
		$file = $this->get($name);
		if ($file) {
			return file_get_contents($file['tmp_name']);
		}
	}
	
	/**
	 * アセットバンドルのファイルをチェックする
	 */
	function checkAssetbundleFile($name)
	{
		// 内容は派生クラスで定義する事
	}
	
    /**
     *  ユーザ定義検証メソッド(フォーム値間の連携チェック等)
     *
     *  @access protected
     */
    function _validatePlus()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');

		// アップロードされたファイルが複数ある場合に、ファイル名の先頭部分やバージョン部分が一致することを確認する
		$file_name = null;
		$version = null;
		foreach (array(
			'asset_bundle_android',
			'asset_bundle_iphone',
			'asset_bundle_pc',
		) as $name) {
			$joint_file_name = $this->getFileName($name);
			if (strlen($joint_file_name) == 0) {
				continue;
			}

			$splitted = Pp_AdminAssetbundleManager::splitFileName($joint_file_name);
			
			// ファイル名を確認
			if ($file_name === null) {
				$file_name = $splitted['file_name'];
			} else if ($file_name != $splitted['file_name']) {
				$this->ae->add($name, "ファイル名が一致しません。", E_ERROR_DEFAULT);
				continue;
			}
			
			// ヴァージョンを確認
			if ($version === null) {
				$version = $splitted['version'];
			} else if ($version != $splitted['version']) {
				$this->ae->add($name, "バージョンが一致しません。", E_ERROR_DEFAULT);
				continue;
			}
		}
    }
}

/**
 *  admin_developer_assetbundle_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleIndex extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/** admin_developer_assetbundle_* で共通の基底アクションクラス */
class Pp_Action_AdminDeveloperAssetbundle extends Pp_AdminActionClass
{
	protected $cache_contents = null;
	
    protected function prepareCacheContents()
    {
		// キャッシュ取得
		$cache_contents = $this->getCacheContents($this->session->get('lid'));

		if (!$cache_contents || Ethna::isError($cache_contents)) {
			$this->af->ae->add(null, "再読込や戻る操作は禁止されています。", E_ERROR_DEFAULT);
			return 'admin_error_400';
		}
		
		if ($cache_contents['confirm_uniq'] != $this->af->get('confirm_uniq')) {
			$this->af->ae->add(null, "再読込や戻る操作は禁止されています。", E_ERROR_DEFAULT);
			return 'admin_error_400';
			// キャッシュが残ったままになるが、特に問題ない。
		}
		
		$this->cache_contents = $cache_contents;
	}
	
	/**
	 * キャッシュ内容を取得する
	 * 
	 * キャッシュはあらかじめ確認画面で生成されている必要がある
	 * @param string $user ユーザー名
	 * @param bool $clear キャッシュクリアするか
	 * @return array キャッシュ内容
	 */
	protected function getCacheContents($user, $clear = true)
	{
		static $cache_contents = null;
		
		static $first = true;
		if (!$first) return $cache_contents;
		$first = false;
		
		// キャッシュ取得
		$cache =& Ethna_CacheManager::getInstance('localfile');
//		$cache_key = $this->session->get('lid') . '_admin_confirm';
		$cache_key = $user . '_admin_confirm';
		$cache_contents = $cache->get($cache_key);

		// キャッシュクリア
		if ($clear && is_array($cache_contents)) {
			$cache->clear($cache_key);
		}

		return $cache_contents;
	}
	
	/**
	 * キャッシュする
	 * 
	 * @param string $user ユーザー名
	 * @param array $cache_contents キャッシュ内容
	 */
	protected function setCacheContents($user, $cache_contents)
	{
		$cache =& Ethna_CacheManager::getInstance('localfile');
//		$cache_key = $this->session->get('lid') . '_admin_confirm';
		$cache_key = $user . '_admin_confirm';
		$cache->set($cache_key, $cache_contents);
	}
}

/**
 *  admin_developer_assetbundle_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleIndex extends Pp_Action_AdminDeveloperAssetbundle
{
    /**
     *  preprocess of admin_developer_assetbundle_index Action.
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
     *  admin_developer_assetbundle_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_assetbundle_index';
    }
}

?>