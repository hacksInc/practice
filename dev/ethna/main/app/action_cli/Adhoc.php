<?php
/**
 *  その場対応
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  adhoc Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_Adhoc extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  adhoc action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_Adhoc extends Pp_CliActionClass
{
    /**
     *  preprocess of adhoc Action.
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
     *  adhoc action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$db =& $this->backend->getDB();

/*
		// JMeterで使う為のBASIC認証情報を一括出力
		// $ ./cli.sh adhoc | unix2dos > base64.csv 
		echo "user_id,plain,base64\n";
//		$result =& $db->query("SELECT user_id FROM t_user_base WHERE '2013-10-10 18:46:00' <= date_created AND date_created < '2013-10-10 21:00:00' AND name IS NULL ORDER BY user_id;");
		$result =& $db->query("SELECT user_id FROM t_user_base WHERE '2013-10-16 20:53:00' <= date_created ORDER BY user_id;");
		while ($row = $result->FetchRow()) {
			$user_id = $row['user_id'];
			
			$plain = $user_id . ':uipw';
			$base64 = base64_encode($plain);
			
			echo '"' . $user_id . '","' , $plain . '","' . $base64 . '"' . "\n";
		}
*/

/*
		// JMeterで使う為のBASIC認証情報を一括出力
		// $ ./cli.sh adhoc
		$filename_head = 'base64_' . date('YmdHi', $_SERVER['REQUEST_TIME']);
		$eol = "\r\n";
		$cnt = 0;
		$cnt2 = 0;
		$limit = 30000;
		$result =& $db->query("SELECT user_id FROM t_user_base WHERE date_created > '2013-11-18 21:00:00' ORDER BY user_id;");
		while ($row = $result->FetchRow()) {
			$user_id = $row['user_id'];
			
			if ($cnt >= $limit) {
				$cnt -= $limit;
				$cnt2++;
			} 
			
			$filename = $filename_head . '_' . $cnt2 . '.csv';
			
			if ($cnt == 0) {
				file_put_contents($filename, '"user_id","plain","base64"' . $eol, FILE_APPEND);
			}
			
			$plain = $user_id . ':uipw';
			$base64 = base64_encode($plain);
			
			$line = '"' . $user_id . '","' . $plain . '","' . $base64 . '"' . $eol;
			file_put_contents($filename, $line, FILE_APPEND);
			
			$cnt++;
		}
*/
		
/*		
		// JMeterで使う為のBASIC認証情報を一括出力
		// $ ./cli.sh adhoc | unix2dos > base64.csv 
		echo "user_id,plain,base64\n";
		foreach (array(
			"910671756" => "766e397646324172694e6723484e41785533506158596b6955654f4b5456315a4a736b5279685a784d686a78456447742547445355376923253676494c6a6c4b",
			"911322586" => "715963625f717a6d4323412323407068474a61254c26644355556b693156526d535f4745507a53257634625278565a23715974583337716f5971334132376e72",
			"911070415" => "4e36534a58306162625f4266776a25495a3450376d26546a40754275693666593246574572464f4f23683671533947664575574630704f4d7374266b67756135",
			"917075651" => "6e5a704f30573066487272625258366a4e7a4f7245695538376c754459746a6349455647307740516f475650346878464c4466547a6c785973562643364f6226",
			"912014050" => "5936554e794665433652234444455025515357725132444a257337715a4e326830596d55476e56627a3576413053436f264d47364556674a4f675665354b4c72",
			"919524361" => "3667354750433265644f43266d4736494a4a5a65534976466170735a6935693077796645684b6751455946437a4255692559356c744171356244694d6e693965",
			"910317482" => "483055377536576247755f46646739734e7459674c7649586759436375435a68614d4e6b64506b4639354a6f3036466347377841513156752552306265595653",
			"912265639" => "4274457a596223766142335343545a4139535a5a6433787861526c5573766c6c71467066656a44414b5232687071367059476458695a712569374143404b4326",
			"916059716" => "536c70725a566d316f6a7559694c45524a3341424a754c4f49377877596a65736e4f5a464a633440655645724d5f54733440584b4523344c5f565271774a5a76",
			"911774787" => "573523787773335035674d33506e65636858514e404b5a4e3230257a736f57434d525452666f553975364a78565a4f33316b5f325131644f49473730436d4372",
		) as $user_id => $uipw) {
			$plain = $user_id . ':' . $uipw;
			$base64 = base64_encode($plain);
			
			echo '"' . $user_id . '","' , $plain . '","' . $base64 . '"' . "\n";
		}
*/
		
/*		
		// JMeterで使う為のBASIC認証情報を一括出力
		// $ ./cli.sh adhoc | unix2dos > base64.csv 
		echo "user_id,plain,base64\n";
		foreach (array(
			"911577701",
			"916404946",
			"911287023",
			"919748760",
			"911505159",
			"917505940",
			"913460368",
			"911758635",
			"911670884",
			"915985780",
		) as $user_id) {
			$plain = $user_id . ':uipw';
			$base64 = base64_encode($plain);
			
			echo '"' . $user_id . '","' , $plain . '","' . $base64 . '"' . "\n";
		}
*/
		
/*		
		// t_user_monsterから、ユーザー毎にdate_modifiedが最新の1件をCSVで取り出す。順番はユーザーID順で。
		// $ ./cli.sh adhoc | unix2dos > monster.csv 
		$header_flg = false;
		$row_prev = null;
		$result =& $db->query("SELECT * FROM t_user_monster ORDER BY user_id ASC, date_modified DESC, user_monster_id DESC");
		while ($row = $result->FetchRow()) {
			if (!$header_flg) {
				echo implode(',', array_keys($row)) . "\n";
				$header_flg = true;
			}
			
			if (!$row_prev || ($row_prev['user_id'] != $row['user_id'])) {
				echo '"' . implode('","', array_values($row)) . '"' . "\n";
			}
			
			$row_prev = $row;
		}
*/
		
/*
		// データ移行パスワード生成
		$user_m = $this->backend->getManager('AdminUser');
		$user_id = 927976353;
		$dmpw = $user_m->getRandomDmpw();
		$dmpw_hash = $user_m->hashDmpw($user_id, $dmpw);
		echo $user_id . ' ' . $dmpw . ' ' . $dmpw_hash . "\n"; 
*/
		
		// ポイント管理サーバへのinquiryのトランザクションについて、t_point_transactionテーブルから削除
/*
error_log("start");
		$filename = BASE . '/tmp/adhoc' . uniqid();
		if (!$fp = fopen($filename, 'w')) {
			 error_log("Cannot open file ($filename)");
			 exit;
		}
	
		$sql = "SELECT game_transaction_id"
		     . " FROM t_point_transaction"
		     . " WHERE point_path = '/Payment/Inquiry'"
		     . " LIMIT 100000";
		$result =& $db->query($sql);
		while ($row = $result->FetchRow()) {
			$line = $row['game_transaction_id'] . "\n";
			if (fwrite($fp, $line) === FALSE) {
				error_log("Cannot write to file ($filename)");
				exit;
			}
		}
		
		fclose($fp);
		
		if (!$fp = fopen($filename, 'r')) {
			 error_log("Cannot open file ($filename)");
			 exit;
		}
		
		$sql = "DELETE FROM t_point_transaction"
		     . " WHERE game_transaction_id = ?";
		
		$cnt = 0;
		while (($line = fgets($fp, 4096)) !== false) {
			$game_transaction_id = rtrim($line);
			
/// *
			$param = array($game_transaction_id);
			if (!$db->execute($sql, $param)) {
				error_log("Cannot delete row ($game_transaction_id)");
				exit;
			}
			
			$affected_rows = $db->db->affected_rows();
			if ($affected_rows != 1) {
				error_log("Invalid affected_rows ($affected_rows)");
				exit;
			}
			
		    error_log("OK: ($game_transaction_id)");
//* /
//error_log("dry-run: ($game_transaction_id)");
			
			$cnt++;
			if (($cnt % 5) == 0) {
//			    error_log("Sleeping.");
				sleep(1);
			}
		}
		
	    if (!feof($fp)) {
		    error_log("Error: unexpected fgets() fail");
			exit;
		}
		
		fclose($fp);
		
		error_log("End. cnt=[$cnt]");
*/

        return null;
    }
}

?>
