<?php
/**
 *  Pp_AdminMessageManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_NewsManager.php';

/**
 *  Pp_AdminMessageManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminMessageManager extends Pp_NewsManager
{
	/** 最後に新規作成されたダイアログメッセージID */
	protected $last_insert_dialog_id = null;

	/** 最後に新規作成されたメインバナーデータの各種ID */
	protected $last_insert_home_banner_id = array(
		'hbanner_id' => null,
		'img_id'     => null,
	);

	// コンストラクタで取得されないDBのインスタンス
	protected $db_m = null;
	protected $db_m_r = null;

	/**
	 *  コンストラクタ
	 */
	function __construct(&$backend) {
		parent::__construct($backend);

		if( is_null( $this->db_m ))
		{	// インスタンスを取得していないなら取得
			$this->db_m =& $this->backend->getDB( 'm' );
		}

		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
	}

	/**
	 * ダイアログメッセージデータを取得する
	 *
	 * @param int $dialog_id 内容ID
	 * @return array m_dialog_messageデータ（m_dialog_messageのカラム名がキー）
	 */
	function getMessageDialog($dialog_id)
	{
		return $this->db_m_r->GetRow(
			"SELECT * FROM m_dialog_message WHERE dialog_id = ? AND del_flg = ?",
			array($dialog_id, 0)
		);
	}

	/**
	 * エラーメッセージデータを取得する
	 *
	 * @param int $error_id 内容ID
	 * @return array m_error_messageデータ（m_error_messageのカラム名がキー）
	 */
	function getMessageError($error_id)
	{
		return $this->db_m_r->GetRow(
			"SELECT * FROM m_error_message WHERE error_id = ? AND del_flg = ?",
			array($error_id, 0)
		);
	}

	/**
	 * ヘルプメッセージデータを取得する
	 *
	 * @param int $help_id 内容ID
	 * @return array m_help_messageデータ（m_help_messageのカラム名がキー）
	 */
	function getMessageHelp($help_id)
	{
		return $this->db_m_r->GetRow(
			"SELECT * FROM m_help_message WHERE help_id = ? AND del_flg = ?",
			array($help_id, 0)
		);
	}

	/**
	 * ダイアログメッセージデータの一覧を取得
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param bool $end 取得対象を表示終了したデータに限定するか
	 * @return array
	 */
	function getMessageDialogList($offset = 0, $limit = 100000, $end = false)
	{
		$param = array();
		$sql = "SELECT dialog_id, dialog_type, use_name, message, date_created, date_modified"
			. " FROM m_dialog_message WHERE del_flg = ?";
/*
		if ($end) {
			$sql .= " WHERE date_end <= NOW()"
				 .  " OR disp_sts = " . self::EVENT_NEWS_CONTENT_DISP_STS_END
				 .  " ORDER BY date_end DESC, content_id DESC"; // 最新の表示終了データを先頭に
		} else {
			$sql .= " WHERE date_end > NOW()"
				 .  " AND disp_sts != " . self::EVENT_NEWS_CONTENT_DISP_STS_END
				 .  " ORDER BY priority ASC, date_disp DESC, content_id DESC";
		}
 */
		$param[] = 0;
		$param[] = $offset;
		$param[] = $limit;
		$sql .= " LIMIT ?, ?";

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * エラーメッセージデータの一覧を取得
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param bool $end 取得対象を表示終了したデータに限定するか
	 * @return array
	 */
	function getMessageErrorList($offset = 0, $limit = 100000, $end = false)
	{
		$param = array();
		$sql = "SELECT error_id, message, date_created, date_modified"
			. " FROM m_error_message WHERE del_flg = ?";
		$param[] = 0;
		$param[] = $offset;
		$param[] = $limit;
		$sql .= " LIMIT ?, ?";

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * ヘルプメッセージデータの一覧を取得
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param bool $end 取得対象を表示終了したデータに限定するか
	 * @return array
	 */
	function getMessageHelpList($offset = 0, $limit = 100000, $end = false)
	{
		$param = array();
		$sql = "SELECT help_id, use_name, message, date_created, date_modified"
			. " FROM m_help_message WHERE del_flg = ?";
/*
		if ($end) {
			$sql .= " WHERE date_end <= NOW()"
				 .  " OR disp_sts = " . self::EVENT_NEWS_CONTENT_DISP_STS_END
				 .  " ORDER BY date_end DESC, content_id DESC"; // 最新の表示終了データを先頭に
		} else {
			$sql .= " WHERE date_end > NOW()"
				 .  " AND disp_sts != " . self::EVENT_NEWS_CONTENT_DISP_STS_END
				 .  " ORDER BY priority ASC, date_disp DESC, content_id DESC";
		}
 */
		$param[] = 0;
		$param[] = $offset;
		$param[] = $limit;
		$sql .= " LIMIT ?, ?";

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * ダイアログメッセージデータの一覧を取得(JSON出力用)
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param bool $end 取得対象を表示終了したデータに限定するか
	 * @return array
	 */
	function getMessageDialogListForJson()
	{
		$param = array();
		$sql = "SELECT dialog_id, message"
			. " FROM m_dialog_message WHERE del_flg = ?";
		$param[] = 0;

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * エラーメッセージデータの一覧を取得(JSON出力用)
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param bool $end 取得対象を表示終了したデータに限定するか
	 * @return array
	 */
	function getMessageErrorListForJson()
	{
		$param = array();
		$sql = "SELECT error_id, message"
			. " FROM m_error_message WHERE del_flg = ?";
		$param[] = 0;

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * ヘルプメッセージデータの一覧を取得(JSON出力用)
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param bool $end 取得対象を表示終了したデータに限定するか
	 * @return array
	 */
	function getMessageHelpListForJson()
	{
		$param = array();
		$sql = "SELECT help_id, message"
			. " FROM m_help_message WHERE del_flg = ?";
		$param[] = 0;

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * ダイアログメッセージデータを更新する
	 *
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateMessageDialog($columns)
	{
		if (!is_numeric($columns['dialog_id'])) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		$where = "dialog_id = " . $columns['dialog_id'];
		unset($columns['dialog_id']);

		return $this->db_m->db->AutoExecute('m_dialog_message', $columns, 'UPDATE', $where);
	}

	/**
	 * エラーメッセージデータを更新する
	 *
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateMessageError($columns)
	{
		if (!is_numeric($columns['error_id'])) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		$where = "error_id = " . $columns['error_id'];
		unset($columns['error_id']);

		return $this->db_m->db->AutoExecute('m_error_message', $columns, 'UPDATE', $where);
	}

	/**
	 * ヘルプメッセージデータを更新する
	 *
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateMessageHelp($columns)
	{
		if (!is_numeric($columns['help_id'])) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		$where = "help_id = " . $columns['help_id'];
		unset($columns['help_id']);

		return $this->db_m->db->AutoExecute('m_help_message', $columns, 'UPDATE', $where);
	}

	/**
	 * ダイアログメッセージデータを新規作成する
	 *
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列。主キーは含めなくてよい）
	 * @return bool 成否
	 */
	function insertMessageDialog($columns)
	{
		if (!isset($columns['dialog_id'])) {
			$max = $this->db_m_r->GetOne("SELECT MAX(dialog_id) FROM m_dialog_message");
			if (!$max) $max = 0;

			$this->last_insert_dialog_id = $columns['dialog_id'] = $max + 1;
		}

		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
			$columns['date_modified'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		return $this->db_m->db->AutoExecute('m_dialog_message', $columns, 'INSERT');
	}

	/**
	 * エラーメッセージデータを新規作成する
	 *
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列。主キーは含めなくてよい）
	 * @return bool 成否
	 */
	function insertMessageError($columns)
	{
		if (!isset($columns['error_id'])) {
			$max = $this->db_m_r->GetOne("SELECT MAX(error_id) FROM m_error_message");
			if (!$max) $max = 0;

			$this->last_insert_error_id = $columns['error_id'] = $max + 1;
		}

		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
			$columns['date_modified'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		return $this->db_m->db->AutoExecute('m_error_message', $columns, 'INSERT');
	}

	/**
	 * ヘルプメッセージデータを新規作成する
	 *
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列。主キーは含めなくてよい）
	 * @return bool 成否
	 */
	function insertMessageHelp($columns)
	{
		if (!isset($columns['help_id'])) {
			$max = $this->db_m_r->GetOne("SELECT MAX(help_id) FROM m_help_message");
			if (!$max) $max = 0;

			$this->last_insert_help_id = $columns['help_id'] = $max + 1;
		}

		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
			$columns['date_modified'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		return $this->db_m->db->AutoExecute('m_help_message', $columns, 'INSERT');
	}

	/**
	 * ダイアログメッセージデータを新規作成する
	 *
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列。主キーは含めなくてよい）
	 * @return bool 成否
	 */
	function insertMessageHelpbar($columns)
	{
		if (!isset($columns['helpbar_id'])) {
			$max = $this->db_m_r->GetOne("SELECT MAX(helpbar_id) FROM m_helpbar_message");
			if (!$max) $max = 0;

			$this->last_insert_helpbar_id = $columns['helpbar_id'] = $max + 1;
		}

		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
			$columns['date_modified'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		return $this->db_m->db->AutoExecute('m_helpbar_message', $columns, 'INSERT');
	}

	/** 最後に新規作成されたダイアログメッセージIDを取得する */
	function getLastInsertDialogId()
	{
		return $this->last_insert_dialog_id;
	}

	/** 最後に新規作成されたメインバナーデータの各種ID値を取得する */
	function getLastInsertHomeBannerId($colname)
	{
		return $this->last_insert_home_banner_id[$colname];
	}

	/**
	 * 改行数のチェックを行う
	 *
	 * @param string $message
	 * @param integer $max_cnt <BR>タグMAX数
	 * @return string $
	 */
	public function checkCountTagByBr($message, $max_cnt)
	{

		$tag_cnt = preg_match_all("/<BR>/i", $message, $matches);

		if ($tag_cnt > $max_cnt) {
			return false;
		}

		return true;
	}

	/**
	 * 1行単位の文字数チェックを行う(カラーコードタグ、改行タグは除いた文字数でチェックを行う)
	 *
	 *
	 * @param string $message
	 * @param integer $max_cnt 最大文字数
	 * @return string $
	 */
	public function checkLineLength($message, $max_cnt)
	{
		$color_code_list = array(
			'/\[000000\]/i',
			'/\[FFCC00\]/i',
			'/\[FFFF00\]/i',
			'/\[008000\]/i',
			'/\[FF0000\]/i',
			'/\[99CC00\]/i',
			'/\[0000FF\]/i',
			'/\[FF00FF\]/i',
			'/\[FF3030\]/i',
			'/\[FFFFFF\]/i',
			'/<BR>/i',
		);

		$origin_message_list = preg_split("/<BR>/i", $message);
		foreach($origin_message_list as $v){
			$message_list = preg_replace($color_code_list, array(), $v);
/*var_dump($message_list);
var_dump(mb_detect_encoding($message_list));
var_dump(mb_strlen($message_list, "UTF-8"));*/
			if ( mb_strlen($message_list, "UTF-8") > $max_cnt ) {
				return false;
			}

		}

		return true;
	}

	/**
	 * 改行文字を<BR>タグに変換する
	 *
	 * @param string $message
	 * @return string $
	 */
	public function convertNewlineCharacterToBr($message)
	{

		$newline_char = array("\r\n","\n\r","\n","\r");
		$convert_message = $message;
		foreach ($newline_char as $v) {
			$tmp_message = preg_replace("/" . $v . "/", "<BR>", $convert_message);
			$convert_message = $tmp_message;
		}

		return $convert_message;
	}
}
