<?php
require_once 'Pp_ShopManager.php';
require_once 'Pp_UserManager.php';

/**
 *	admin_developer_gacha_* で共通のアクションフォーム定義
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */
class Pp_Form_AdminDeveloperGacha extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'gacha_id' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'ガチャID', // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 1, 			  // Minimum value
				'max'		  => null,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
				//	Filter
				'filter'	  => null,			  // Optional Input filter to convert input
				'custom'	  => null,			  // Optional method name which
												  // is defined in this(parent) class.
			),

			'type' => array(
				// フォームの定義
				'type'		=> VAR_TYPE_INT, 
				'form_type' => FORM_TYPE_SELECT,
				'name'		=> 'ガチャタイプ',

				// バリデータ(記述順にバリデータが実行されます)
				'required'	=> true,			// 必須オプション(true/false)
				'option'	=> array(
					Pp_ShopManager::GACHA_TYPE_BRONZE  => 'ブロンズガチャ',
					Pp_ShopManager::GACHA_TYPE_GOLD    => 'ゴールドガチャ',
					Pp_ShopManager::GACHA_TYPE_MEDAL   => 'マジカルメダルガチャ',
					Pp_ShopManager::GACHA_TYPE_EVENT   => 'イベントガチャ',
					Pp_ShopManager::GACHA_TYPE_MEDAL11 => 'マジカルメダル11連ガチャ',
					Pp_ShopManager::GACHA_TYPE_EVENT11 => 'イベント11連ガチャ',
				),
			),
			
			'price' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => '価格',		  // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 0, 			  // Minimum value
				'max'		  => null,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
				//	Filter
				'filter'	  => null,			  // Optional Input filter to convert input
				'custom'	  => null,			  // Optional method name which
												  // is defined in this(parent) class.
			),
			
			'comment' => array(
				// Form definition
				'type'		  => VAR_TYPE_STRING, // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => '運営用メモ',	  // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => false, 		  // Required Option(true/false)
				'min'		  => 0, 			  // Minimum value
				'max'		  => 256,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 

				//	Filter
				'filter'	  => null,			  // Optional Input filter to convert input
				'custom'	  => null,			  // Optional method name which
												  // is defined in this(parent) class.
			),

			'disp_sts' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => '表示ステータス', // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,
				'min'		  => Pp_ShopManager::GACHA_DISP_STS_NORMAL, 
				'max'		  => Pp_ShopManager::GACHA_DISP_STS_END, 
			),
			
			'sort_list' => array(
				// フォームの定義
				'type'		=> VAR_TYPE_INT, 
				'form_type' => FORM_TYPE_SELECT,
				'name'		=> 'ソート順',

				// バリデータ(記述順にバリデータが実行されます)
				'required'	=> true,
				'option'	=> array(
					 1 => '1',
					 2 => '2',
					 3 => '3',
					 4 => '4',
					 5 => '5',
					 6 => '6',
					 7 => '7',
					 8 => '8',
					 9 => '9',
				),
			),
			
			'banner_type' => array(
				// フォームの定義
				'type'		=> VAR_TYPE_INT, 
				'form_type' => FORM_TYPE_SELECT,
				'name'		=> 'バナータイプ',

				// バリデータ(記述順にバリデータが実行されます)
				'required'	=> true,			// 必須オプション(true/false)
				'option'	=> array(
					Pp_ShopManager::GACHA_BANNER_TYPE_NONE		  => 'なし',
					Pp_ShopManager::GACHA_BANNER_TYPE_QUEST_MAP   => 'クエストマップ',
					Pp_ShopManager::GACHA_BANNER_TYPE_EVENT_QUEST => 'イベントクエスト',
					Pp_ShopManager::GACHA_BANNER_TYPE_SHOP_GACHA  => 'ショップ（ガチャ）',
					Pp_ShopManager::GACHA_BANNER_TYPE_SHOP_ITEM   => 'ショップ（アイテム）',
					Pp_ShopManager::GACHA_BANNER_TYPE_SHOP_MEDAL  => 'ショップ（マジカルメダル）',
					Pp_ShopManager::GACHA_BANNER_TYPE_ACHIEVEMENT => '勲章',
					Pp_ShopManager::GACHA_BANNER_TYPE_RANKING	  => 'ランキング',
					Pp_ShopManager::GACHA_BANNER_TYPE_URL		  => 'URL',
					Pp_ShopManager::GACHA_BANNER_TYPE_WEBVIEW	  => 'WEBVIEW',
				),
			),
			
			'banner_url' => array(
				// Form definition
				'type'		  => VAR_TYPE_STRING, // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'バナーURL',	  // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => false, 		  // Required Option(true/false)
				'min'		  => 0, 			  // Minimum value
				'max'		  => 512,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 

				//	Filter
				'filter'	  => null,			  // Optional Input filter to convert input
				'custom'	  => 'checkURL,checkHostEnv',	  // Optional method name which
												  // is defined in this(parent) class.
			),

			'ua' => array(
				// フォームの定義
				'type'		=> VAR_TYPE_INT, 
				'form_type' => FORM_TYPE_SELECT,
				'name'		=> '対応OS',

				// バリデータ(記述順にバリデータが実行されます)
				'required'	=> true,			// 必須オプション(true/false)
				'option'	=> array(
					 Pp_UserManager::OS_IPHONE		   => 'iOS',
					 Pp_UserManager::OS_ANDROID 	   => 'Android',
					 Pp_UserManager::OS_IPHONE_ANDROID => 'iOS / Android',
				),
			),

			'width' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'WebView横幅',   // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 0, 			  // Minimum value
				'max'		  => null,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
				//	Filter
				'filter'	  => null,			  // Optional Input filter to convert input
				'custom'	  => null,			  // Optional method name which
												  // is defined in this(parent) class.
			),
			
			'height' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'WebView縦幅',   // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 0, 			  // Minimum value
				'max'		  => null,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
				//	Filter
				'filter'	  => null,			  // Optional Input filter to convert input
				'custom'	  => null,			  // Optional method name which
												  // is defined in this(parent) class.
			),
			
			'position_x' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'WebView横座標',	// Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => -9999, 		  // Minimum value
				'max'		  => null,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
				//	Filter
				'filter'	  => null,			  // Optional Input filter to convert input
				'custom'	  => null,			  // Optional method name which
												  // is defined in this(parent) class.
			),
			
			'position_y' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'WebView縦座標',	// Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => -9999, 		  // Minimum value
				'max'		  => null,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
				//	Filter
				'filter'	  => null,			  // Optional Input filter to convert input
				'custom'	  => null,			  // Optional method name which
												  // is defined in this(parent) class.
			),

			'banner_uploaded' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	   // Input type
				'form_type'   => FORM_TYPE_HIDDEN, // Form type

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 0, 			  // Minimum value
				'max'		  => 1, 			  // Maximum value
			),

			'banner_image' => array(
				// Form definition
				'type'		  => VAR_TYPE_FILE,   // Input type
				'form_type'   => FORM_TYPE_FILE,  // Form type
				'name'		  => 'バナー画像',	   // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => null,			  // Minimum value
				'max'		  => null,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 

				//	Filter
				'filter'	  => null,			  // Optional Input filter to convert input
				'custom'	  => null,			  // Optional method name which
												  // is defined in this(parent) class.
			),
			
			'rarity' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'レアリティ',	   // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 1, 			  // Minimum value
				'max'		  => 99,			  // Maximum value
			),
			
			'rarities' => array(
				// Form definition
				'type'		  => array(VAR_TYPE_INT), // Input type
				'form_type'   => FORM_TYPE_TEXT,	  // Form type
				'name'		  => 'レアリティ',		  // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 1, 			  // Minimum value
				'max'		  => null,			  // Maximum value
			),

//			'weight' => array(
//				// Form definition
//				'type'		  => VAR_TYPE_INT,	  // Input type
//				'form_type'   => FORM_TYPE_TEXT,  // Form type
//				'name'		  => 'ウェイト',	 // Display name
//
//				//	Validator (executes Validator by written order.)
//				'required'	  => true,			  // Required Option(true/false)
//				'min'		  => 0, 			  // Minimum value
//				'max'		  => null,			  // Maximum value
//			),
			
			'weight_float' => array(
				// Form definition
				'type'		  => VAR_TYPE_FLOAT, // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
//				'name'		  => 'ウェイト（小数2桁）', 	 // Display name
				'name'		  => 'ウェイト',	  // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 0, 			  // Minimum value
				'max'		  => null,			  // Maximum value
			),
			
//			'weights' => array(
//				// Form definition
//				'type'		  => array(VAR_TYPE_INT), // Input type
//				'form_type'   => FORM_TYPE_TEXT,	  // Form type
//				'name'		  => 'ウェイト',		  // Display name
//
//				//	Validator (executes Validator by written order.)
//				'required'	  => true,			  // Required Option(true/false)
//				'min'		  => 0, 			  // Minimum value
//				'max'		  => null,			  // Maximum value
//			),
			
			'weights_float' => array(
				// Form definition
				'type'		  => array(VAR_TYPE_FLOAT), // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
//				'name'		  => 'ウェイト（小数2桁）', 	 // Display name
				'name'		  => 'ウェイト',	  // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 0, 			  // Minimum value
				'max'		  => null,			  // Maximum value
			),
			
			'monster_id' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'モンスターID',	 // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 1, 			  // Minimum value
				'max'		  => null,			  // Maximum value
			),
			
			'monster_lv' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'モンスターLV',	 // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 1, 			  // Minimum value
				'max'		  => null,			  // Maximum value
			),
			
			'order_id' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'オーダーID',	   // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => false, 		  // Required Option(true/false)
				'min'		  => 1, 			  // Minimum value
				'max'		  => null,			  // Maximum value
			),
			
			'deck' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'デッキ数',	  // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 1, 			  // Minimum value
				'max'		  => null,			  // Maximum value
			),
			
			'date_draw_start' => array(
				// Form definition
				'type'		  => VAR_TYPE_STRING,  // Input type
				'form_type'   => FORM_TYPE_TEXT,   // Form type
				'name'		  => '取得日付',	   // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => false, 		  // Required Option(true/false)
				'min'		  => null,			  // Minimum value
				'max'		  => null,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
				//	Filter
				'filter'	  => 'space_zentohan,numeric_zentohan,ltrim,rtrim', // Optional Input filter to convert input
				'custom'	  => 'checkDatetime9999', // Optional method name which
													  // is defined in this(parent) class.
			),

			'date_draw_end' => array(
				// Form definition
				'type'		  => VAR_TYPE_STRING,  // Input type
				'form_type'   => FORM_TYPE_TEXT,   // Form type
				'name'		  => '取得日付',	   // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => false, 		  // Required Option(true/false)
				'min'		  => null,			  // Minimum value
				'max'		  => null,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
				//	Filter
				'filter'	  => 'space_zentohan,numeric_zentohan,ltrim,rtrim', // Optional Input filter to convert input
				'custom'	  => 'checkDatetime9999', // Optional method name which
													  // is defined in this(parent) class.
			),
		
		);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}
		
		$this->form_template['date_start']['name'] = '公開開始日時';
		$this->form_template['date_end']['name']   = '公開終了日時';
		
		parent::__construct($backend);
	}

	/**
	 * 指定されたレアリティがガチャカテゴリに存在しないことをチェックする
	 */
	function checkGachaCatgoryRarityNotExists($name)
	{
		$rarity = $this->form_vars[$name];
		$gacha_id = $this->form_vars['gacha_id'];
		
		$shop_m =& $this->backend->getManager('AdminShop');
	
		$is_exists = $shop_m->isGachaCatgoryExists($gacha_id, $rarity);
		if ($is_exists) {
			$this->ae->add($name, "同じレアリティ設定は行えません");
		}
	}
	
	/**
	 * 指定されたレアリティがおまけガチャカテゴリに存在しないことをチェックする
	 */
	function checkGachaExtraCatgoryRarityNotExists($name)
	{
		$rarity = $this->form_vars[$name];
		$gacha_id = $this->form_vars['gacha_id'];
		
		$shop_m =& $this->backend->getManager('AdminShop');
	
		$is_exists = $shop_m->isGachaExtraCatgoryExists($gacha_id, $rarity);
		if ($is_exists) {
			$this->ae->add($name, "同じレアリティ設定は行えません");
		}
	}
	
	/**
	 * 指定されたモンスターIDがガチャアイテムリストに存在することをチェックする
	 */
	function checkGachaItemMonsterIdExists($name)
	{
		$monster_id = $this->form_vars[$name];
		$gacha_id	= $this->form_vars['gacha_id'];
		$monster_lv	= $this->form_vars['monster_lv'];
		$rarity 	= $this->form_vars['rarity'];
		
		$shop_m =& $this->backend->getManager('AdminShop');
	
		$gacha_item_exists = $shop_m->isGachaItemExists($gacha_id, $rarity, $monster_id, $monster_lv);
		if (!$gacha_item_exists) {
			$this->ae->add($name, "指定LVのモンスターIDがガチャアイテムリストマスタに登録されていません");
		}
	}

	/**
	 * 指定されたモンスターIDがおまけガチャアイテムリストに存在することをチェックする
	 */
	function checkGachaExtraItemMonsterIdExists($name)
	{
		$monster_id = $this->form_vars[$name];
		$gacha_id	= $this->form_vars['gacha_id'];
		$monster_lv	= $this->form_vars['monster_lv'];
		$rarity 	= $this->form_vars['rarity'];
		
		$shop_m =& $this->backend->getManager('AdminShop');
	
		$gacha_item_exists = $shop_m->isGachaExtraItemExists($gacha_id, $rarity, $monster_id, $monster_lv);
		if (!$gacha_item_exists) {
			$this->ae->add($name, "指定LVのモンスターIDがおまけガチャアイテムリストマスタに登録されていません");
		}
	}

	
	/**
	 * 指定されたモンスターIDがガチャアイテムとして新規作成可能かチェックする
	 */
	function checkGachaItemMonsterIdCreatable($name)
	{
		$monster_id = $this->form_vars[$name];
		$monster_lv = $this->form_vars['monster_lv'];
		$gacha_id	= $this->form_vars['gacha_id'];
		$rarity 	= $this->get('rarity');
		
		$shop_m =& $this->backend->getManager('AdminShop');
		$monster_m =& $this->backend->getManager('Monster');

		$monster = $monster_m->getMasterMonster($monster_id);
		$monster_exists = (is_array($monster) && (count($monster) > 0));
		if (!$monster_exists) {
			$this->ae->add($name, "モンスターIDがモンスターマスタに登録されていません");
		}

		if (is_numeric($rarity) && ($rarity != $monster['m_rare'])) {
			$this->ae->add($name, "レアリティが不正です");
		}
		
		$gacha_item_exists = $shop_m->isGachaItemExists($gacha_id, $monster['m_rare'], $monster_id, $monster_lv);
		if ($gacha_item_exists) {
			$this->ae->add($name, "指定LVのモンスターIDが既にガチャアイテムリストマスタに登録されています");
		}
	}
	

	/**
	 * 指定されたモンスターIDがおまけガチャアイテムとして新規作成可能かチェックする
	 */
	function checkGachaExtraItemMonsterIdCreatable($name)
	{
		$monster_id = $this->form_vars[$name];
		$monster_lv = $this->form_vars['monster_lv'];
		$gacha_id	= $this->form_vars['gacha_id'];
		$rarity 	= $this->get('rarity');
		
		$shop_m =& $this->backend->getManager('AdminShop');
		$monster_m =& $this->backend->getManager('Monster');

		$monster = $monster_m->getMasterMonster($monster_id);
		$monster_exists = (is_array($monster) && (count($monster) > 0));
		if (!$monster_exists) {
			$this->ae->add($name, "モンスターIDがモンスターマスタに登録されていません");
		}

		if (is_numeric($rarity) && ($rarity != $monster['m_rare'])) {
			$this->ae->add($name, "レアリティが不正です");
		}
		
		$gacha_item_exists = $shop_m->isGachaExtraItemExists($gacha_id, $monster['m_rare'], $monster_id, $monster_lv);
		if ($gacha_item_exists) {
			$this->ae->add($name, "指定LVのモンスターIDは既におまけガチャアイテムリストマスタに登録されています");
		}
	}

	/**
	 * 指定されたモンスターLVが最大レベルを超えていないかチェックする
	 */
	function checkGachaMonsterMaxLv( $name )
	{
		$monster_id = $this->form_vars['monster_id'];
		$monster_lv = $this->form_vars[$name];

		$monster_m =& $this->backend->getManager('Monster');
		$monster_data = $monster_m->getMasterMonster( $monster_id );
		if( empty( $monster_data ) === true )
		{
			$this->ae->add( $name, "モンスターIDがモンスターマスタに登録されていません" );
		}
		if( $monster_lv > $monster_data['max_lv'] )
		{	// 入力値が最大レベルを超えている
			$this->ae->add( $name, "最大レベルを超えた設定はできません" );
		}
	}

	/**
	 *	ユーザ定義検証メソッド(フォーム値間の連携チェック等)
	 *
	 *	@access protected
	 */
	function _validatePlus()
	{
		$rarities = $this->get('rarities');
		$weights_float = $this->get('weights_float');
		if ($rarities || $weights_float) {
			if (count($rarities) !== count($weights_float)) {
				$this->ae->add(null, 'レアリティまたはウェイトが不正です', E_FORM_INVALIDVALUE);
			}
		}
	}

	/**
	 * テンポラリのバナー画像ファイル名を取得する
	 * 
	 * @param string $confirm_uniq 確認画面で生成したユニーク値
	 * @return string ファイル名（フルパス）
	 */
	function getAdminTmpBannerFilename($confirm_uniq)
	{
		return BASE . '/tmp/admin_tmp_gacha_banner_' . $confirm_uniq;
	}
	
	/**
	 * レアリティからウェイトを求める
	 * 
	 * 配列で渡される場合用
	 */
	function getWeightFloatByRarity($rarity)
	{
		$rarities = $this->get('rarities');
		$weights_float = $this->get('weights_float');
		
		$key = array_search($rarity, $rarities);
		if ($key !== false) {
			return $weights_float[$key];
		}
	}
}

?>