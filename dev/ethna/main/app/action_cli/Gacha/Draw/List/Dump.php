<?php
/**
 *  ガチャドローリストをダンプする
 *
 *  旧テーブル(t_gacha_draw_list)を参照して、新テーブル(log_gacha_draw_list)へのINSERT文としてダンプする
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  gacha_draw_list_dump Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_GachaDrawListDump extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  gacha_draw_list_dump action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_GachaDrawListDump extends Pp_CliActionClass
{
    /**
     *  preprocess of gacha_draw_list_dump Action.
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
     *  gacha_draw_list_dump action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// 引数取得
		if ( $GLOBALS['argc'] < 4 ) {
			// パラメータ不足
			Ethna::raiseError( 'Too few parameter.', E_GENERAL );
			error_log('Too few parameter.');
			return;
		} else {
			// 第2引数以降を格納する
			$arg_start = $GLOBALS['argv'][2]; // Y-m-d（端点含む）
			$arg_end   = $GLOBALS['argv'][3]; // Y-m-d（端点含む）
		}
		
		$date_draw_start = $arg_start . ' 00:00:00';
		$date_draw_end   = $arg_end   . ' 23:59:59';
		
		$admin_m = $this->backend->getManager('Admin');
		$shop_m  = $this->backend->getManager('AdminShop');

		$db_r =& $this->backend->getDB('r');
		
		$admin_m->offSessionQueryCache();
		$admin_m->setSessionSqlBigSelectsOn(array('r'));
		$admin_m->setAdodbCountrecs(false);

		$param = array($date_draw_start, $date_draw_end);
		$sql = "SELECT gacha_id, rarity, monster_id, user_id, date_created, date_draw"
		     . " FROM t_gacha_draw_list"
		     . " WHERE date_draw IS NOT NULL"
			 . " AND date_draw >= ?"
			 . " AND date_draw <= ?";
//			 . " ORDER BY date_draw";//ユニットが複数だと仮定すると各ユニットでソートしてもかえって紛らわしいのでORDER BYしない
		
		$result =& $db_r->query($sql, $param);
		
		while ($row = $result->FetchRow()) {
			$this->bulkEcho($row);
		}
		
		$this->bulkEcho(null, true);
		
        return null;
    }
	
	const BULK_THRESHOLD = 10;
		
	protected function bulkEcho($row, $flush = false)
	{
		static $clauses = array();
		static $cnt = 0;
		
		if ($row) {
			$clause = "(" . $row['gacha_id'] . "," . $row['rarity'] . "," . $row['monster_id'] . ",0," . $row['user_id'] . ",'" . $row['date_draw'] . "',NOW())";
			$clauses[] = $clause;
			$cnt += 1;
		}
		
		if (($cnt >= self::BULK_THRESHOLD) || ($flush && ($cnt > 0))) {
			$sql = "INSERT INTO log_gacha_draw_list(gacha_id,rarity,monster_id,monster_lv,user_id,date_draw,date_created)" . "\n"
			     . "VALUES" . implode(",\n", $clauses) . ";\n";
			echo $sql;
			
			$clauses = array();
			$cnt = 0;
		}
    }
}

?>
