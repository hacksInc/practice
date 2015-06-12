<?php
/**
 *  モンスター図鑑データを変換する
 *
 *  旧テーブル(t_user_monster_book)から新テーブル(t_user_monster_book_bits)への変換を行なう
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  user_monster_book_convert Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_UserMonsterBookConvert extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  user_monster_book_convert action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_UserMonsterBookConvert extends Pp_CliActionClass
{
	protected $db = null;
	
    /**
     *  preprocess of user_monster_book_convert Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  user_monster_book_convert action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$monster_m = $this->backend->getManager('AdminMonster');
		$admin_m = $this->backend->getManager('Admin');
		
		$admin_m->offSessionQueryCache();
		$this->db =& $this->backend->getDB();

		error_log("start");
		
		// 全ユーザIDを取得
		$filename = BASE . '/tmp/convert' . uniqid();
		if (!$fp = fopen($filename, 'w')) {
			 error_log("Cannot open file ($filename)");
			 exit;
		}
	
		$sql = "SELECT user_id FROM t_user_base";
		$result =& $this->db->query($sql);
		while ($row = $result->FetchRow()) {
			$line = $row['user_id'] . "\n";
			if (fwrite($fp, $line) === FALSE) {
				error_log("Cannot write to file ($filename)");
				exit;
			}
		}
		
		fclose($fp);

		// ユーザごとに処理
		if (!$fp = fopen($filename, 'r')) {
			 error_log("Cannot open file ($filename)");
			 exit;
		}
		
		$cnt = 0;
		while (($line = fgets($fp, 4096)) !== false) {
			$user_id = rtrim($line);

			// 一定頻度でsleep（DB負荷対応として）
			if (($cnt++ % 10) == 0) {
				sleep(1);
			}
			
			// 移行対象ユーザか判定
			if (!$this->isIntendedUser($user_id)) {
			    error_log("OK(skip) $user_id");
				continue;
			}

			// DBから読み込む
			$book = $monster_m->getUserMonsterBookAssoc($user_id);
			if (!is_array($book) || empty($book)) {
				error_log("Error: no data. $user_id");
				continue;
			}
			
			// DBへ保存する
			$ret = $monster_m->saveUserMonsterBookBits($user_id, false);
			if ($ret !== true) {
				error_log("Error: saveUserMonsterBookBits failed. Maybe concurrent access. $user_id");
				continue;
			}

		    error_log("OK(done) $user_id");
			
			// PHP内の変数を解放する
			$monster_m->clearUserMonsterBookVar($user_id);
		}
		
	    if (!feof($fp)) {
		    error_log("Error: unexpected fgets() fail");
			exit;
		}
		
		fclose($fp);
		
		error_log("End. cnt=[$cnt]");
		
        return null;
    }
	
	// 移行対象ユーザーか
	protected function isIntendedUser($user_id)
	{
		// t_user_monster_bookテーブルのデータ有無を確認
		$user_id_book = $this->db->GetOne("SELECT user_id FROM t_user_monster_book WHERE user_id = ? LIMIT 1", array($user_id));
		if (Ethna::isError($user_id_book)) {
			// ERROR
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}
		
		if (!$user_id_book) { // 無かったら
			return false; // 対象ではない
		}
		
		// t_user_monster_book_bitsテーブルのデータ有無を確認
		$user_id_bits = $this->db->GetOne("SELECT user_id FROM t_user_monster_book_bits WHERE user_id = ?", array($user_id));
		if (Ethna::isError($user_id_bits)) {
			// ERROR
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}
		
		if ($user_id_bits) { // 有ったら
			return false; // 対象ではない
		}
		
		return true;
	}
}

?>