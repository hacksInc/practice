<?php
/**
 *  Kpi/Monster/Use.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  kpi_monster_use Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_KpiMonsterUse extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  kpi_monster_use action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_KpiMonsterUse extends Pp_CliActionClass
{
    /**
     *  preprocess of kpi_monster_use Action.
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
     *  kpi_monster_use action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// 引数取得
		if ($GLOBALS['argc'] < 3) {
			$this->usage();
//			return;
            exit(1);
		}
		
		$tran_date = $GLOBALS['argv'][2];

		// リソース初期化
        $kpiview_m = $this->backend->getManager('KpiViewMonster');
		
		list($year, $month, $day) = explode('/', $tran_date);
		$date = $year . '-' . $month . '-' . $day . ' 00:00:00';

        // データの集計を行いDBに登録
        $res = $kpiview_m->makeKpiMonsterUse($date);
        $this->backend->logger->log(LOG_INFO, '******************************** argv' . print_r($res, true));
        if ($res !== true) {
            exit(1);
        }

        $this->backend->logger->log(LOG_INFO, '******************************** end');
        exit(0);
    }
	
	protected function usage()
	{
		error_log('Usage: kpi_monster_use_rate.sh ENV_TYPE LOG_DATE');
	}
}
