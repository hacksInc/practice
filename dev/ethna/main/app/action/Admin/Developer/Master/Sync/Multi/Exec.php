<?php
/**
 *  Admin/Developer/Master/Sync/Multi/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Pp_Form_AdminDeveloperMasterSync.php';

/**
 *  admin_developer_master_sync_multi_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperMasterSyncMultiExec extends Pp_Form_AdminDeveloperMasterSync
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'mode' => array(
            'type'        => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => true,                 // Required Option(true/false)
            'min'         => null,                 // Minimum value
            'max'         => 16,                   // Maximum value
            'regexp'      => '/^deploy|standby|unitsync$/', // String by Regexp
        ),
		'tables',
		'algorithms',
		'all_sync',
    );
}

/**
 *  admin_developer_master_sync_multi_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperMasterSyncMultiExec extends Pp_AdminActionClass
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
     *  admin_developer_master_sync_multi_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$mode = $this->af->get('mode');
		
		//unitsyncは通常処理と分ける
		if ($mode === 'deploy' || $mode === 'standby') {
			$action_name = 'admin_developer_master_sync_exec';
			$forward_name = $this->backend->perform($action_name);
			if ($forward_name != $action_name) {
				return $forward_name;
			}
		}
		//unitsyncの処理
    	if ($mode === 'unitsync') {
			$developer_m =& $this->backend->getManager('Developer');
			$unit_m =& $this->backend->getManager('Unit');
			$all_sync = $this->af->get('all_sync');
			//指定されたテーブルのみ同期させる
			if ($all_sync != 1) {
				$tables = $this->af->get('tables');
				$list = array();
				$unit_all = $this->config->get('unit_all');
				$unit = $this->session->get('unit');
				//error_log("unit=$unit");
				//テーブル単位でループ
				//気が狂いそうになる程のforeachのネストが続く
				foreach ($tables as $table) {
					//スキーマが異なる場合の動作は保証しない
					$list[$table] = $developer_m->getMetadata($table);
					//error_log($table."=".print_r($list[$table],true));
					$sql = "SELECT * FROM $table";
					$param = null;
					$data = array();
					$data_fc = array();//for check用
					//各ユニットのマスタデータを全件取得する
					foreach ($unit_all as $unit_no => $unit_info) {
						$data[$unit_no] = $unit_m->getAllSpecificUnit($sql, $param, $unit_no);
						if ($unit_no != $unit) {
							foreach ($data[$unit_no] as $dk => $dv) {
								$data_fc[$unit_no][$dk] = false;
							}
						}
					}
					//プライマリキーを取得
					$primary_keys = $list[$table]['primary_keys'];
					//現在のユニットとのキー単位で差分（存在するか否か）を調べる
					foreach ($unit_all as $unit_no => $unit_info) {
						//現在のユニット以外の時に処理を行う
						if ($unit_no != $unit) {
							//error_log("unit $unit => unit $unit_no");
							//現在のユニットのデータ
							foreach ($data[$unit] as $s_key => $s_val) {
								$s_primary_key = array();
								foreach ($primary_keys as $pkey) {
									$s_primary_key[$pkey] = $s_val[$pkey];
								}
								$exist = false;
								//存在を確認できるまでループして探す
								foreach ($data[$unit_no] as $d_key => $d_val) {
									//既にチェック済のデータはスキップする
									if ($data_fc[$unit_no][$d_key] == true) continue;
									$d_primary_key = array();
									//全てのキーが一致するか？
									foreach ($primary_keys as $pkey) {
										$d_primary_key[$pkey] = $d_val[$pkey];
										if ($d_primary_key[$pkey] != $s_primary_key[$pkey]) {
											$exist = false;
											break;
										} else {
											$exist = true;
										}
									}
									//同じキーのデータが存在していた
									if ($exist) {
										$data_fc[$unit_no][$d_key] = true;
										break;
									}
								}
								$param = array();
								//同じキーのデータが存在していたからUPDATEする
								if ($exist) {
									$sql = 'UPDATE '. $table .' SET ';
									$first = true;
									foreach ($s_val as $svkey => $svval) {
										if (!$first) $sql .= ', ';
										$sql .= "$svkey=?";
										$param[] = $svval;
										$first = false;
									}
									$first = true;
									$sql .= " WHERE ";
									foreach ($s_primary_key as $s_pkey => $s_pval) {
										if (!$first) $sql .= 'AND ';
										$sql .= "$s_pkey=? ";
										$param[] = $s_pval;
										$first = false;
									}
									//error_log("sql=[$sql] param=".print_r($param, true));
								} else {//INSERTする
									$sql_i = 'INSERT INTO '. $table .' (';
									$sql_v = ') VALUES (';
									$first = true;
									foreach ($s_val as $svkey => $svval) {
										if (!$first) {
											$sql_i .= ', ';
											$sql_v .= ', ';
										}
										$sql_i .= "$svkey";
										$sql_v .= "?";
										$param[] = $svval;
										$first = false;
									}
									$sql = $sql_i . $sql_v . ')';
									//error_log("sql=[$sql] param=".print_r($param, true));
								}
								//ターゲットのユニットへINSERTまたはUPDATEする
								$ret = $unit_m->executeForUnit($unit_no, $sql, $param);
								if (!$ret || Ethna::isError($ret)) {
									$this->af->setAppNe('err_msg', $ret);
									$this->af->setApp('err_table', $table);
									$this->af->setApp('err_sql', $sql);
									$this->af->setApp('err_param', $param);
									return 'admin_developer_master_sync_multi_error';
								}
							}
							//現在のユニットに存在しないデータを削除する
							//error_log("unit $unit_no chk=".print_r($data_fc[$unit_no],true));
							foreach ($data_fc[$unit_no] as $fck => $fcv) {
								if (!$fcv) {
									$d_val = $data[$unit_no][$fck];
									$d_primary_key = array();
									foreach ($primary_keys as $pkey) {
										$d_primary_key[$pkey] = $d_val[$pkey];
									}
									$param = array();
									$sql = 'DELETE FROM '. $table .' WHERE ';
									$first = true;
									foreach ($d_primary_key as $d_pkey => $d_pval) {
										if (!$first) $sql .= 'AND ';
										$sql .= "$d_pkey=? ";
										$param[] = $d_pval;
										$first = false;
									}
									//error_log("sql=[$sql] param=".print_r($param, true));
									//ターゲットのユニットからDELETEする
									$ret = $unit_m->executeForUnit($unit_no, $sql, $param);
									if (!$ret || Ethna::isError($ret)) {
										$this->af->setAppNe('err_msg', $ret);
										$this->af->setApp('err_table', $table);
										$this->af->setApp('err_sql', $sql);
										$this->af->setApp('err_param', $param);
										return 'admin_developer_master_sync_multi_error';
									}
								}
							}
						}
					}
				}
			} else {
				//全マスタテーブルを同期させる
				//1.現在のユニットのm_*テーブルをエクスポートする
				//2.同期先ユニットのm_*テーブルをtruncateする
				//3.エクスポートしたデータを同期先ユニットへインポートする
				$unit_all = $this->config->get('unit_all');
				$unit = $this->session->get('unit');
				$src_dsn = $this->config->get('dsn');
				//ex)'mysqli://httpd:thadfrRMzh87@dbiajmja-master/jmjamaindev'
				$src_dsn_prm = split('[:/.@]', $src_dsn);
				$user = $src_dsn_prm[3];
				$passwd = $src_dsn_prm[4];
				$host = $src_dsn_prm[5];
				$dbname = $src_dsn_prm[6];
				//マスタテーブル一覧を取得
				$sql = "SHOW TABLES WHERE Tables_in_$dbname LIKE 'm\_%' AND Tables_in_$dbname NOT LIKE '%\_bak'";
				$data = $unit_m->getAllSpecificUnit($sql, null, $unit);
				//error_log(print_r($data,true));
				$m_tables = '';
				foreach ($data as $table) {
					//error_log("unit $unit dump ".$table["Tables_in_$dbname"]);
					$m_tables .= $table["Tables_in_$dbname"].' ';
				}
				$path = BASE . '/tmp';
				$filename = 'unit'.$unit.'_m_'.date('YmdHis').'.sql';
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
				//1.現在のユニットのm_*テーブルをエクスポートする
				$cmd = "/usr/bin/mysqldump -u $user --password=$passwd --lock-tables=0 --add-locks=0 --add-drop-table=0 --skip-disable-keys --no-create-info --complete-insert --compact -h $host $dbname $m_tables > $path/$filename";
				//error_log("cmd=$cmd");
				exec($cmd, $arr, $ret);
				if ($ret != 0) {
					$this->af->setApp('err_msg', 'error code='.$ret);
					$this->af->setApp('err_table', 'dump');
					$this->af->setApp('err_sql', $cmd);
					$this->af->setApp('err_param', '');
					return 'admin_developer_master_sync_multi_error';
				}
				//error_log("system return=$ret array=($arr)=".print_r($arr,true));
				//2.同期先ユニットのm_*テーブルをtruncateする
				foreach ($unit_all as $unit_no => $unit_info) {
					//現在のユニット以外の時に処理を行う
					if ($unit_no != $unit) {
						//error_log("unit $unit => unit $unit_no");
						$dest_dsn = $unit_info['dsn'];
						//ex)'mysqli://httpd:thadfrRMzh87@dbiajmja-master/jmjamaindev02'
						$dest_dsn_prm = split('[:/.@]', $dest_dsn);
						$host = $dest_dsn_prm[5];
						$dbname = $dest_dsn_prm[6];
						//マスタテーブル一覧を取得
						$sql = "SHOW TABLES WHERE Tables_in_$dbname LIKE 'm\_%' AND Tables_in_$dbname NOT LIKE '%\_bak'";
						$data = $unit_m->getAllSpecificUnit($sql, null, $unit_no);
						//error_log(print_r($data,true));
						//空にする
						foreach ($data as $table) {
						//	$sql = "TRUNCATE ".$table["Tables_in_$dbname"];
							$sql = "DELETE FROM ".$table["Tables_in_$dbname"];//httpdユーザではTRUNCATE出来ないのでDELETEで代用
							$ret = $unit_m->executeForUnit($unit_no, $sql, null);
						//	error_log("sql=[$sql] ret=".print_r($ret,true));
						}
					}
				}
				//3.エクスポートしたデータを同期先ユニットへインポートする
				//ex)mysql -u admin -h dbiajmja-master --password=`cat ~/dbauth/admin`
				$cmd = "/usr/bin/mysql -u $user --password=$passwd -h $host -D $dbname < $path/$filename";
				//error_log("cmd=$cmd");
				$rec = exec($cmd, $arr, $ret);
				//error_log("system return=$ret rec=$rec array=($arr)=".print_r($arr,true));
				if ($ret != 0) {
					$this->af->setApp('err_msg', 'error code='.$ret);
					$this->af->setApp('err_table', 'unknown');
					$this->af->setApp('err_sql', $cmd);
					$this->af->setApp('err_param', $m_tables);
					return 'admin_developer_master_sync_multi_error';
				} else {
					exec("rm $path/$filename");
				}
			}
		}

        return 'admin_developer_master_sync_multi_exec';
    }
}

?>