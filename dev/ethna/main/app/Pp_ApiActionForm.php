<?php
// vim: foldmethod=marker
/**
 *  Pp_ApiActionForm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Blowfish.class.php';

// {{{ Pp_ApiActionForm
/**
 *  ApiActionForm class.
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_ApiActionForm extends Ethna_ActionForm
{
	/**#@+
	 *  @access private
	 */

	/**
	 * パスワード種別
	 * 
	 * ※'uipw'は使わなくした。この設定は必ず'appw'になる。（2013/03/21）
	 * @var string 'appw'（アプリパスワード） or 'uipw'（ユニークインストールパスワード） 
	 */
	protected $password_type = 'appw';

	protected $output_names = array();
	
	protected $output_names_no_convert = array();
	
	protected $blowfish_key_hex = null;
	
	protected $input_json;

	/** @var    array   form definition (default) */
	var $form_template = array(
	   /*
		*  @see http://www.ethna.jp/ethna-document-dev_guide-form_template.html
		*
		*  Example(You can omit all elements except for "type" one) :
		*
		*  'sample' => array(
		*      // Form definition
		*      'type'        => VAR_TYPE_INT,    // Input type
		*      'form_type'   => FORM_TYPE_TEXT,  // Form type
		*      'name'        => 'Sample',        // Display name
		*  
		*      //  Validator (executes Validator by written order.)
		*      'required'    => true,            // Required Option(true/false)
		*      'min'         => null,            // Minimum value
		*      'max'         => null,            // Maximum value
		*      'regexp'      => null,            // String by Regexp
		*      'mbregexp'    => null,            // Multibype string by Regexp
		*      'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		*
		*      //  Filter
		*      'filter'      => 'sample',        // Optional Input filter to convert input
		*      'custom'      => null,            // Optional method name which
		*                                        // is defined in this(parent) class.
		*  ),
		*/

		'shop_id' => array(
			// Form definition
			'type'        => VAR_TYPE_INT,    // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => null,            // Maximum value
		),

		'item_id' => array(
			// Form definition
			'type'        => VAR_TYPE_INT,    // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => null,            // Maximum value
		),
		
		'consume_id' => array(
			// Form definition
			'type'        => VAR_TYPE_INT,    // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => null,            // Maximum value
		),

		'game_transaction_id' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 255,             // Maximum value
		),
		
		'receipt_product_id' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 64,              // Maximum value
		),

		'google_transaction_or_apple_receipt' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 65535,           // Maximum value
		),
		
		'google_signature' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 65535,           // Maximum value
		),
		
		'app_id' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 10,              // Maximum value
		),
		
		'device_info' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => 0,               // Minimum value
			'max'         => 65535,           // Maximum value
		),
		
		// ↓psycho-pass追加分
		'transaction_id' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 255,             // Maximum value
		),
	);
	
	// バリデート用カスタム関数は以下にまとめる
	
	function checkUa($name, $value = null)
	{
		if ($value === null) {
			$value = $this->get($name);
		}

		$user_m = $this->backend->getManager('User');
		
		if (!is_numeric($value) || !(
			($value == Pp_UserManager::OS_IPHONE) ||
			($value == Pp_UserManager::OS_ANDROID)
		)) {
			$this->ae->add($name, "Invalid Ua.", E_FORM_INVALIDCHAR);
		}
	}

	function checkTeamId($name, $value = null)
	{
		if ($value === null) {
			$value = $this->get($name);
		}

		//チームID（0～4）
		if (!(is_numeric($value) && (0 <= $value) && ($value <= 4))) {
			$this->ae->add($name, "Invalid TeamId.", E_FORM_INVALIDCHAR);
		}
	}
	
	function checkUserMonsterId($name, $value = null)
	{
		if ($value === null) {
			$value = $this->get($name);
		}

		if (!(is_numeric($value) && (-2 <= $value) && (strlen($value) <= 20))) { // 20 == strlen('18446744073709551616'), 左記の巨大な値は2の64乗（MySQLのBIGINT相当）
			$this->ae->add($name, "Invalid UserMonsterId.", E_FORM_INVALIDCHAR);
		}
	}
	
	/** 場所をチェック（1始まり） */
	function checkPosition($name, $value = null)
	{
		if ($value === null) {
			$value = $this->get($name);
		}

		//位置（左から1～5）
		if (!(is_numeric($value) && (1 <= $value) && ($value <= 5))) {
			$this->ae->add($name, "Invalid Position.", E_FORM_INVALIDCHAR);
		}
	}
	
	/** 場所をチェック（0始まり） */
	function checkPositionZeroStart($name, $value = null)
	{
		if ($value === null) {
			$value = $this->get($name);
		}

		//位置（左から0～4）
		if (!(is_numeric($value) && (0 <= $value) && ($value <= 4))) {
			$this->ae->add($name, "Invalid Position.", E_FORM_INVALIDCHAR);
		}
	}
	
	function checkFlg($name, $value = null)
	{
		if ($value === null) {
			$value = $this->get($name);
		}

		if (!(is_numeric($value) && (0 <= $value) && ($value <= 1))) {
			$this->ae->add($name, "Invalid Flg.", E_FORM_INVALIDCHAR);
		}
	}
	
	function checkUserTeam($name, $value = null)
	{
		if ($value === null) {
			$value = $this->get($name);
		}

		foreach ($value as $k => $v) {
			switch ($k) {
				case "team_id"://チームID（0～4）,
					$this->checkTeamId(null, $v);
					break;

				case "pos1"://モンスターユニークID,
				case "pos2"://モンスターユニークID,
				case "pos3"://モンスターユニークID,
				case "pos4"://モンスターユニークID,
				case "pos5"://モンスターユニークID,
					$this->checkUserMonsterId(null, $v);
					break;

				case "leader"://リーダーの位置（左から0～4）,
					$this->checkPositionZeroStart(null, $v);
					break;

				case "selected_flg"://選択フラグ...
					$this->checkFlg(null, $v);
					break;
			}
		}
	}

	function checkUserTeamList($name, $value = null)
	{
		if ($value === null) {
			$value = $this->get($name);
		}
		
		if (!$value) {
			//OK
			return;
		}
		
		if (!is_array($value)) {
			$this->ae->add($name, "Invalid UserTeamList.", E_FORM_INVALIDCHAR);
		}
		
		foreach ($value as $team_id => $row) {
			$this->checkTeamId(null, $team_id);
			$this->checkUserTeam(null, $row);
		}
	}

	/**
	 * チームのリーダーの位置を補正
	 * 
	 * リーダーの位置のユーザーモンスターIDが空やフレンドの場合は補正する
	 * エラーの場合はアクションエラーへの追加も行なう
	 * @param array $columns チーム情報。API引数の"user_team"内の1行分のデータと同じ書式（キーが"leader","pos1","pos2","pos3","pos4","pos5"の連想配列）で
	 * @return array 補正したチーム情報
	 */
	protected function adjustUserTeamLeader($columns)
	{
		$this->backend->logger->log(LOG_DEBUG, 'adjustUserTeamLeader. columns=[' . var_export($columns, true) . ']');
		
		$team_m = $this->backend->getManager('Team');
		
		if (!is_array($columns)) {
			return $columns;
		}

		// リーダーの位置が指定されていない場合、チェックしない
		if (!isset($columns["leader"]) || !is_numeric($columns["leader"])) {
			return $columns;
		}

		$leader_position = $columns["leader"]; // リーダーの位置
		$leader_key = "pos" . ($leader_position + 1);

		// リーダーの位置が指定されている場合、全ての位置のユーザーモンスターIDが指定されていなかったらエラー
		for ($tmp_position = 0; $tmp_position < Pp_TeamManager::MAX_POSITION; $tmp_position++) {
			$tmp_key = "pos" . ($tmp_position + 1);
			if (!isset($columns[$tmp_key])) {
				$this->ae->add('user_team', "Invalid team. [" . $tmp_key . "]", E_FORM_INVALIDCHAR);
				return $columns;
			}
		}

		// 指定されたリーダーの位置のユーザーモンスターIDを求める
		$leader_user_monster_id = $columns[$leader_key];

		// 指定されたリーダーの位置のユーザーモンスターIDがUSER_MONSTER_ID_EMPTY, USER_MONSTER_ID_HELPERのいずれかでなかったら補正不要
		if (($leader_user_monster_id != Pp_TeamManager::USER_MONSTER_ID_EMPTY) &&
			($leader_user_monster_id != Pp_TeamManager::USER_MONSTER_ID_HELPER)
		) {
			// OK
			return $columns;
		}

		// ここまで来るのは、補正が必要な場合
		// 位置の番号が若い順に、USER_MONSTER_ID_EMPTY, USER_MONSTER_ID_HELPER以外で最初に現れるユーザーモンスターIDをリーダーにする
		$adjusted = false;
		$new_columns = $columns;
		for ($tmp_position = 0; $tmp_position < Pp_TeamManager::MAX_POSITION; $tmp_position++) {
			$tmp_key = "pos" . ($tmp_position + 1);
			if (!isset($columns[$tmp_key])) {
				$this->backend->logger->log(LOG_WARNING, $tmp_key . ' not set.');
				continue;
			}

			$tmp_user_monster_id = $columns[$tmp_key];
			if (($tmp_user_monster_id == Pp_TeamManager::USER_MONSTER_ID_EMPTY) ||
				($tmp_user_monster_id == Pp_TeamManager::USER_MONSTER_ID_HELPER)
			) {
				continue;
			}

			$new_columns["leader"] = $tmp_position;
			$adjusted = true;
			break;
		}

		if (!$adjusted) {
			$this->ae->add('user_team', "Invalid team leader. [" . $leader_key . "]", E_FORM_INVALIDCHAR);
		}
		
		$this->backend->logger->log(LOG_WARNING, 'Invalid team leader. adjusted=[' . ($adjusted ? 1 : 0) . '] columns=[' . var_export($columns, true) . '] new_columns=[' . var_export($new_columns, true) . ']');
		
		return $new_columns;
	}
	
/*
	function checkPaymentTransaction($name, $value = null)
	{
		// transaction:
		//   google発行のレシート
		//   google発行のjsonをbase64エンコード
		//   ※1 androidのみ 
		
		if ($value === null) {
			$value = $this->get($name);
		}
		
		$len = strlen($value);
		if ($len === 0) {
			//OK
			return;
		}
		
		if ($len <= 0) {
			$this->ae->add($name, "Invalid transaction.", E_FORM_INVALIDCHAR);
		}
	}
	
	function checkPaymentSignature($name, $value = null)
	{
		// signature:
		//   google発行のレシート検証用コード
		//   google発行値そのまま
		//   ※1 androidのみ 
		
		if ($value === null) {
			$value = $this->get($name);
		}

		$len = strlen($value);
		if ($len === 0) {
			//OK
			return;
		}
		
		if ($len <= 0) {
			$this->ae->add($name, "Invalid signature.", E_FORM_INVALIDCHAR);
		}
	}
	
	function checkPaymentReceipt($name, $value = null)
	{
		// receipt:
		//   apple発行レシート
		//   apple発行値
		//   ※2 iphoneのみ 
		
		if ($value === null) {
			$value = $this->get($name);
		}

		$len = strlen($value);
		if ($len === 0) {
			//OK
			return;
		}
		
		if ($len <= 0) {
			$this->ae->add($name, "Invalid receipt.", E_FORM_INVALIDCHAR);
		}
	}
	
	function checkPaymentCoin($name, $value = null)
	{
		// coin:
		//   コイン額
		//   数値のみ（１以上） 
		
		if ($value === null) {
			$value = $this->get($name);
		}
	
		if (!is_numeric($value) || ($value < 1)) {
			$this->ae->add($name, "Invalid coin.", E_FORM_INVALIDCHAR);
		}
	}
	
	function checkPaymentService($name, $value = null)
	{
		// service:
		//   サービス額
		//   数値のみ（コインのみのときは０を指定）
		
		if ($value === null) {
			$value = $this->get($name);
		}
	
		if (!is_numeric($value) || ($value < 0)) {
			$this->ae->add($name, "Invalid service.", E_FORM_INVALIDCHAR);
		}
	}
*/
	
	/**
	 *  ユーザから送信されたフォーム値をフォーム値定義に従ってインポートする
	 *
	 *  暗号化JSONデータの処理も行なう
	 *  @access public
	 */
    function setFormVars()
    {
        parent::setFormVars();

        if (!isset($_POST['c'])) {
            // OK
            return;
        }
        
		// base64パラメータが存在し、商用環境でなく且つ値がfalse（文字列）のときのみ、Authorizationパラメータのbase64decodeを無視
		$headers = getAllheaders();
//		if ( isset( $headers['base64'] ) && $headers['base64'] == 'false' && $this->backend->config->get( "is_test_site" ) == 1 ) {
		if ( isset( $headers['jmeter'] ) && $this->backend->config->get( "is_test_site" ) == 1 ) {
			$base64_flg = false;

			$stress_test_user = ( isset( $headers['stress-test-user'] )&&( $headers['stress-test-user'] == 1 )) ? true : false;

			$pp_base = 940000000;
			$pp_offs = ( $stress_test_user ) ? 20000000 : 0;

			// jmeter用サイコパスID範囲に差し替える
			$unit_all = $this->backend->config->get('unit_all');
			$unit_all['1']['ppid_range']['min'] = $pp_base + $pp_offs + 1;
			$unit_all['1']['ppid_range']['max'] = $pp_base + $pp_offs + 9999999;
			$unit_all['1']['uid_range']['min']  = $pp_base + $pp_offs + 1;
			$unit_all['1']['uid_range']['max']  = $pp_base + $pp_offs + 9999999;
			$unit_all['2']['ppid_range']['min'] = $pp_base + $pp_offs + 1 + 10000000;
			$unit_all['2']['ppid_range']['max'] = $pp_base + $pp_offs + 9999999 + 10000000;
			$unit_all['2']['uid_range']['min']  = $pp_base + $pp_offs + 1 + 10000000;
			$unit_all['2']['uid_range']['max']  = $pp_base + $pp_offs + 9999999 + 10000000;
			$this->backend->config->set('unit_all', $unit_all);
		} else if ( isset( $headers['trial'] ) ) {
			// 体験版
			$pp_base = 980000000;
			$unit_all = $this->backend->config->get('unit_all');
			$unit_all['1']['ppid_range']['min'] = $pp_base + 1;
			$unit_all['1']['ppid_range']['max'] = $pp_base + 999999;
			$unit_all['1']['uid_range']['min']  = $pp_base + 1;
			$unit_all['1']['uid_range']['max']  = $pp_base + 999999;
			$unit_all['2']['ppid_range']['min'] = $pp_base + 1 + 1000000;
			$unit_all['2']['ppid_range']['max'] = $pp_base + 999999 + 1000000;
			$unit_all['2']['uid_range']['min']  = $pp_base + 1 + 1000000;
			$unit_all['2']['uid_range']['max']  = $pp_base + 999999 + 1000000;
			$this->backend->config->set('unit_all', $unit_all);
			$this->backend->config->set('trial', 1);
			$base64_flg = true;
			$stress_test_user = false;
			error_log('trial *************************************');
		} else {
			$base64_flg = true;
			$stress_test_user = false;
		}
		parent::setApp( "auth_base64_flg", $base64_flg );
		parent::setApp( "stress_test_user", $stress_test_user );

		// base64_flg:falseの場合はパラメータを復号化しない
		if ( $base64_flg ) {
			$this->input_json = $this->_filter_blowfish_decrypt($_POST['c']);
		} else {
			// stripslashes必須？
			$this->input_json = stripslashes( $_POST['c'] );
		}

		// 負荷テスト対応
		// 負荷テスト終了後はコメントアウトすること
/*		
		if (is_stress_test()) {
			if (isset($_POST['stress_test_json'])) {
				$json = $_POST['stress_test_json'];
				$this->logger->log(LOG_INFO, 'Stress test Json:' . $json);
			}
		}
*/
        if (!$this->input_json) {
            return;
        }

        $params = json_decode($this->input_json, true);
		
        if (!is_array($params)) {
			$this->logger->log(LOG_INFO, 'json_decode failed.');
			return;
        }
        
        foreach ($params as $key => $value) {
            // $valueが配列でもそのまま格納してしまうので、バリデート時に要注意
            $this->form_vars[$key] = $value;
        }
    }

	function getInputJson()
	{
		return $this->input_json;
	}

	public function getBlowfishKeyHex()
	{
		return $this->blowfish_key_hex;
	}
	
	/**
	 *  Form input value convert filter : blowfish_decrypt
	 *
	 *  @access protected
	 *  @param  mixed   $value  Form Input Value
	 *  @return mixed           Converted result.
	 */
	function _filter_blowfish_decrypt($value)
	{
		switch ($this->password_type) {
			case 'appw':
				$hex = $this->backend->config->get('appw');
				break;
				
			case 'uipw':
				$hex = $this->getRequestedBasicAuth('password');
				break;
		}

		// Blowfishクラス
		$blowfish = new Blowfish();
		$blowfish->SetKey($hex);
		$this->blowfish_key_hex = $hex;

		// Blowfishを解読する
//		$this->logger->log(LOG_DEBUG, 'Input Blowfish:' . $value);
		$value = $blowfish->Decrypt($value);
//		$this->logger->log(LOG_DEBUG, 'Input Base64:' . $value);
		$value = base64_decode($value);
		$this->logger->log(LOG_INFO, 'Input Json:' . $value);
		
		return $value;
	}
	
/*
	// この関数はobsolute
	function getC($name)
	{
//		$c = $this->get('c');
//		if ($c && isset($c[$name])) {
//			return $c[$name];
//		}
		
		return $this->get($name);
	}
*/
	
	/**
	 * 
	 * @param type $name
	 * @param type $value
	 * @param bool $output ビューでの暗号化JSON出力対象に含めるか
	 * @param bool $no_convert ビューでの暗号化JSON出力時に型変換せずそのまま出力するか
	 * @return type
	 */
	function setApp($name, $value, $output = false, $no_convert = false)
	{
		if ($output) {
			if ($no_convert) {
				if (!in_array($name, $this->output_names_no_convert)) {
					$this->output_names_no_convert[] = $name;
				}
			} else {
				if (!in_array($name, $this->output_names)) {
					$this->output_names[] = $name;
				}
			}
		}
		
		return parent::setApp($name, $value);
	}
	
	function getOutputNames()
	{
		return $this->output_names;
	}

	function getOutputNamesNoConvert()
	{
		return $this->output_names_no_convert;
	}

	/**
	 * HTTPリクエストに含まれるBASIC認証ユーザー名またはパスワードを取得
	 * 
	 * 正しいユーザー名，パスワードかどうかは、この関数では検証しないので注意。
	 * なぜか $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] が存在しないので、
	 * 自前で処理を記述した。
	 * @param string $colname 取得したいカラム名（'user' or 'password'）
	 * @return string
	 */
	function getRequestedBasicAuth($colname)
	{
		static $auth = null;

		if ($auth === null) {
			$headers = getallheaders();
			if (isset($headers['Authorization'])) {
				if ( parent::getApp( "auth_base64_flg" ) ) {
					list($user, $password) = explode(':', 
						base64_decode(substr($headers['Authorization'], 6)), 2 // 6 == strlen('Basic ')
					);
				} else {
					list($user, $password) = explode(':', 
						substr($headers['Authorization'], 6), 2 // 6 == strlen('Basic ')
					);
				}
				$this->logger->log(LOG_INFO, 'Authorization header exists.');
				$auth = array(
					'user' => $user,
					'password' => $password,
				);
			} elseif ( isset( $headers['authorization'] ) ) { // 端末依存の小文字対策
				if ( parent::getApp( "auth_base64_flg" ) ) {
					list($user, $password) = explode(':', 
						base64_decode(substr($headers['authorization'], 6)), 2 // 6 == strlen('Basic ')
					);
				} else {
					list($user, $password) = explode(':', 
						substr($headers['authorization'], 6), 2 // 6 == strlen('Basic ')
					);
				}
				$this->logger->log(LOG_INFO, 'Authorization header exists.');
				$auth = array(
					'user' => $user,
					'password' => $password,
				);
			} else {
				if ( $this->backend->config->get( "is_test_site" ) == 1 ) {
					$auth = array(
						'user' => 915694803, 
						'password' => 'b833a427b974bddcf3ea66188d80f4537e97cd2e',
//						'user' => null, 
//						'password' => null
					);
				} else {
					$this->logger->log(LOG_INFO, 'No Authorization header.');
					$auth = array(
						'user' => null, 
						'password' => null
					);
				}
			}
		}
		
//error_log( "auth:" . print_r( $auth, 1 ) );

		return $auth[$colname];
	}
}
// }}}

?>
