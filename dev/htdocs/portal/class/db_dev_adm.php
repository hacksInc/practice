<?php
#class.php

//**************************************************************
// データベースクラス
//**************************************************************
class db {
	//	プロパティ定義
	private $mysqli;
	private $stmt;
	private $result;
	private $row;
	private $debug;
	
	private $sql_str;
	private $bind_arr;
	private $bind_type_str;
	private $rows;
	
	private $insert_id;
	
	//***************************
	// コンストラクタ
	// ※クラス生成時に呼ばれる
	//***************************
	public function __construct(){
		$this->initDebug();
		$this->initBindParam();
	}
	
	//***************************
	// デストラクタ
	// ※クラスを参照しなくなった時に呼ばれる
	//***************************
	public function __destruct() {
		if ($this->stmt) $this->closeStmt();
		if ($this->mysqli) $this->mysqli->close();
	}
	
	//***************************
	// 初期化メソッド
	//***************************
	//	デバッグモード初期化
	public function initDebug() {
		$this->debug = FALSE;
	}
	//	バインドパラメータの初期化
	public function initBind_arr() {
		$this->bind_arr = array();
	}
	//	バインドパラメータのタイプの初期化
	public function initBind_type_str() {
		$this->bind_type_str = "";
	}
	//	バインドパラメータの初期化
	function initBindParam($param=NULL) {
		if ($param) {
			$param = array("type" => "", "bind" => array());
			return $param;
		} else {
			$this->initBind_arr();
			$this->initBind_type_str();
		}
	}
	
	//***************************
	// ゲッター
	//***************************
	//	SQL文を取得
	public function getSql_str() {
		return $this->sql_str;
	}
	//	SQLの結果の入ったresultを取得
	public function getResult() {
		return $this->result;
	}
	//	行数を取得
	public function getRows($result=NULL) {
		if ($result) {
			$stmt = $result['stmt'];
			return $stmt->num_rows;
		} else {
			return $this->rows;
		}
	}
	//	UPDATE/DELETE/INSERT で影響のあった行数を取得
	public function getAffectedRows($result=NULL) {
		if ($result) {
			$stmt = $result['stmt'];
			return $stmt->affected_rows;
		} else {
			return -1;
		}
	}
	//	INSERT で auto_increment された項目の値を取得
	public function getInsertID() {
		return $this->insert_id;
	}
	
	//***************************
	// セッター
	//***************************
	//	デバッグモードをセット
	public function setDebug($bool) {
		$this->debug = $bool;
	}
	//	SQL文をセット
	public function setSql_str($sql_str) {
		$this->sql_str = $sql_str;
	}
	//	バインドパラメータをセット
	public function setBind_arr($bind_arr) {
		$this->bind_arr = $bind_arr;
	}
	//	行数をセット
	public function setRows($rows) {
		$this->rows = $rows;
	}
	
	//***************************
	// 処理メソッド
	//***************************
	//	DB接続
	function connectDB() {
		//	DB情報　ここの情報はdefineなどで別ファイルで管理してたらいいかも
		$sv = "dbptpyco-master-new";
		$dbname = "psychopass_game_main_master_dev";
		$user = "httpd";
		$passwd = "qkn84CorIqfl";
		
		$mysqli = new mysqli($sv, $user, $passwd, $dbname);
		
		//	DB接続チェック
		if (mysqli_connect_errno()) {
			//エラー表示
			$this->dispError(mysqli_connect_error());
			exit();
		}
		$mysqli->set_charset("utf8"); // 文字化け防止
		$this->mysqli = $mysqli;
		
		//	auto commit を false にセット
		$mysqli->autocommit(FALSE);
	}
	//	DB切断
	function closeDB(){
		$this->mysqli->close();
	}
	//	stmt解放
	function closeStmt($result=NULL){
		if ($result) {
			$stmt = $result['stmt'];
		}else{
			$stmt = $this->stmt;
		}
		//$stmt->free_result();
		$stmt->close();
	}
	//	バインドパラメータ追加
	function addBind($param=NULL, $type, $val) {
		//	HTMLエンティティをデコード
		$val = htmlspecialchars_decode($val);
		
		$this->bind_type_str .= $type;
		array_push($this->bind_arr, $val);
		
		if (!$param) {
			$param = array ("type" => "", "bind" => array() );
		}
		$param['type'] .= $type;
		array_push($param['bind'], $val);
		
		return $param;
	}
	//	クエリ実行
	function exeQuery($param=NULL) {
		//	mysqliセット
		$mysqli = $this->mysqli;
		
		//	SQL文取得
		$sql = $this->getSql_str();
		
		//	バインドパラメータ取得
		if ($param) {
			$bind_arr = $param['bind'];
			array_unshift($bind_arr, $param['type']);
			
		} else {
			$bind_arr = $this->bind_arr;
			array_unshift($bind_arr, $this->bind_type_str);
		}
		
		//	返り値の格納オブジェクト
		$result = new stdClass();
		
		//	ステートメント準備
		if ($stmt = $mysqli->prepare($sql)) {
			//	バインド処理
			call_user_func_array(array($stmt, 'bind_param'), $this->refValues($bind_arr));
			
			//	SQL実行
			$stmt->execute();
			
			//	auto_increment でセットされた項目の値をセット（SELECT や auto_increment の項目がない場合は、0 をセット）
			$this->insert_id = $mysqli->insert_id;
			if (empty($this->insert_id) || $this->insert_id <= 0) {
				$this->insert_id = $stmt->insert_id;
			}
			
			//	エラー表示
			$this->dispError($stmt->error, $param);
			
			//	結果を保存
			$stmt->store_result();
			
			//	行数をセット
			$this->setRows($stmt->num_rows);
				
			//	関連付け
			$row = array();
			$this->stmtBindAssoc($stmt, $row);
			
			//	セット
			$this->stmt = $stmt;
			$this->row = $row;
			
			$return_arr = array("stmt" => $this->stmt, "row" => $this->row);
			return $return_arr;
		} else {
			//	エラー表示
			$this->dispError($mysqli->error, $param);
			return NULL;
		}
	}
	//	実行結果を関連づける
	function stmtBindAssoc(&$stmt, &$out) {
		$data = mysqli_stmt_result_metadata($stmt);
		$fields = array();
		$out = array();
		
		$fields[0] = $stmt;
		$count = 1;
		
		while ($field = mysqli_fetch_field($data)) {
			$fields[$count] = &$out[$field->name];
			$count++;
		}
		call_user_func_array("mysqli_stmt_bind_result", $this->refValues($fields));
	}
	function refValues($arr){
		if (strnatcmp(phpversion(),'5.3') >= 0) //	Reference is required for PHP 5.3+
		{
			$refs = array();
			foreach($arr as $key => $value)
				$refs[$key] = &$arr[$key];
			return $refs;
		}
		return $arr;
	}
	//	フェッチ処理
	function exeFetch($result=NULL){
		if ($result) {
			$stmt = $result['stmt'];
			$row = $result['row'];
		} else {
			$stmt = $this->stmt;
			$row= $this->row;
		}
		
		if ($stmt->fetch()) {
			//	HTMLエンティティを変換
			return array_map("htmlspecialchars", $row);
		}else{
			return NULL;
		}
	}
	//	エラー表示
	function dispError($error, $param=NULL) {
		if (!$error) return;
		
		//	デバッグモードON時
		if ($this->debug) {
			//	SQLがある時
			if ($this->getSql_str()) {
				echo "SQL = [ ".$this->getSql_str()." ]";
				echo "'?' in SQL = [ ".substr_count($this->getSql_str(), "?")." ]";
			}
			
			//	バインドパラメータがある時
			if ($param) {
				echo "Bind Param count = [ ".count($param['bind'])." ]";
				echo "Bind Param type = [ ".$param['type']." ]";
				echo "Bind Param detail = [ ";
				print_r($param['bind']);
				echo " ]";
			}
			
			echo "Error: ".$error;
			exit();
		}
	}
	//	SQL文表示　※?に値を入れて表示する
	function dispSql($result=NULL, $param=NULL) {
		//	SQLがある時
		if ($this->getSql_str()) {
			$sql = $this->getSql_str();
			$bind = $param['bind'];
			if ($bind) {
				foreach ($bind as $n => $val) {
					$sql = preg_replace("/\?/", "'".$val."'", $sql, 1);
				}
			}
			echo "SQL = [".$sql."]";
		}
		
		//	検索結果数表示
		if ($result) {
			echo "rows = [".$this->getRows($result)."]";
		}
	}
	
	//***************************
	// コミット＆ロールバック	//	TODO 未テスト
	//***************************
	/**
	 *	コミット
	 *	return int	error 発生時は 9
	 */
	function commit() {
		//	mysqliセット
		$mysqli = $this->mysqli;
		
		//	commit
		if (!$mysqli->commit()) {
			return(9);
		}
		return(1);
	}
	/**
	 *	ロールバック
	 *	return int	error 発生時は 9
	 */
	function rollback() {
		//	mysqliセット
		$mysqli = $this->mysqli;
		
		//	rollback
		if (!$mysqli->rollback()) {
			return(9);
		}
		return(1);
	}
}
?>
