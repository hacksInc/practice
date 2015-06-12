<?php
/**
 *  Admin/Developer/Master/Output/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

class Pp_Form_AdminDeveloperMasterOutputExec extends Pp_AdminActionForm
{
	var $form = array(
		'kind' => array(
			// Form definition
			// VAR_TYPE_INTにすると空文字列を渡されたときにエラーになるので文字列として扱う
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'kind',            // Display name
		
			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'regexp'      => '/^data$|^schema$/', // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
		),
	);
}

/**
 *  admin_developer_master_output_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperMasterOutputExec extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_developer_master_sync_multi_exec Action.
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
	 *  admin_developer_master_output_exec action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$db = $this->backend->getDB('m_r');
		$dsn = $db->parseDSN($db->dsn);

		$user = $dsn['username'];
		$passwd = $dsn['password'];
		$host = $dsn['hostspec'];
		$dbname = $dsn['database'];

		$kind = $this->af->get('kind');

		//マスタテーブル一覧を取得
		$sql = "SHOW TABLES WHERE Tables_in_$dbname LIKE 'm\_%' AND Tables_in_$dbname NOT LIKE '%\_bak'";
		$data = $db->GetAll($sql);

		$m_tables = '';
		foreach ($data as $table) {
			//error_log("unit $unit dump ".$table["Tables_in_$dbname"]);
			$m_tables .= $table["Tables_in_$dbname"].' ';
		}
		$path = BASE . '/tmp';
		$filename = 'master_'.$kind.'_'.date('YmdHis').'.sql';
	/*
		$user = 'admin';
		//パスワードをファイルから取得
		$fp = fopen(BASE . "/../dbauth/$user", 'r');
		$passwd = fgets($fp);
		$passwd = str_replace(array("\r\n","\r","\n"), '', $passwd);
		fclose($fp);
	*/
		//error_log("path=$path filename=$filename");
		//exec("chmod 666 $path/$filename");
		switch ($kind) {
			case 'data':
				$cmd = "/usr/bin/mysqldump -u $user --password=$passwd --lock-tables=0 --add-locks=0 --add-drop-table=0 --skip-disable-keys --no-create-info --complete-insert --compact -h $host $dbname $m_tables > $path/$filename";
				break;
			case 'schema':
				$cmd = "/usr/bin/mysqldump -u $user --password=$passwd --lock-tables=0 --add-locks=0 --add-drop-table=0 --no-data -h $host $dbname $m_tables > $path/$filename";
				break;
		}
		//error_log("cmd=$cmd");
		exec($cmd, $arr, $ret);
		if ($ret != 0) {
			$this->af->setApp('err_msg', 'error code='.$ret);
			$this->af->setApp('err_table', 'dump');
			$this->af->setApp('err_sql', $cmd);
			$this->af->setApp('err_param', '');
			return 'admin_developer_master_output_error';
		}
		//出力
		header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename=" . $filename );

		$file = "$path/$filename";

		header('Content-Length: ' . filesize($file));
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		ob_clean();
		flush();
		readfile($file);
		unlink($file);

	}
}

?>