<?php
/**
 *  DBをバックアップする
 *
 *  BASE/bin下のシェルスクリプト経由でmysqldumpを実行する
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  db_backup Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_DbBackup extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  db_backup action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_DbBackup extends Pp_CliActionClass
{
    /**
     *  preprocess of db_backup Action.
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
     *  db_backup action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$db =& $this->backend->getDB();		
		
		// 引数取得
		if ($GLOBALS['argc'] < 3) {
			$this->usage();
			return;
		}
		
		$mode = $GLOBALS['argv'][2]; // 動作モード

		// コンフィグからバックアップ対象DSNを取得
		$dsn_list = array();
		$unit_all = $this->config->get('unit_all');
		switch ($mode) {
			case 'master_data':
				foreach ($unit_all as $unit => $columns) {
					$dsn_list[] = $columns['dsn'];
				}

				break;
				
			case 'nodata':
			case 'full':
				foreach ($unit_all as $unit => $columns) {
					foreach (array('dsn', 'dsn_log') as $key) {
						$dsn_list[] = $columns[$key];
					}
				}

				foreach (array('dsn_cmn', 'dsn_logex') as $key) {
					$dsn_list[] = $this->config->get($key);
				}
				
				break;
			
			default:
				// 引数不正
				$this->usage();
				return;
		}
        
        $dsn_src = $this->config->get('dsn_src');
        if (!empty($dsn_src)) {
            $dsn_list[] = $dsn_src;
        }
		
		// バックアップ実行
		// backup_db_master_data.sh, backup_db_nodata.sh, backup_db_full.sh のいずれかを実行する
		$script = BASE . "/bin/backup_db_$mode.sh";
//		$script = "echo " . BASE . "/bin/backup_db_$mode.sh";
		foreach ($dsn_list as $dsn) {
			$parsed = $db->parseDSN($dsn);
			if (!is_array($parsed) || 
				!isset($parsed['hostspec']) || !($parsed['hostspec']) ||
				!isset($parsed['database']) || !($parsed['database'])
			) {
				error_log('Invalid dsn.');
				continue;
			}
			
			$command = implode(" ", array($script, $parsed['hostspec'], $parsed['database']));
			system($command);
		}
		
        return null;
    }
	
	protected function usage()
	{
		error_log('Usage: cli.sh db_backup mode');
		error_log('(Valid mode is "master_data" or "nodata" or "full")');
	}
}
