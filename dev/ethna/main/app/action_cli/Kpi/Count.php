<?php
/**
 *  KPI集計を行う
 *
 *  1時間ごとの集計、および所定の時刻・日付の場合は1日ごとや1ヶ月ごとの集計を行う
 *  （並列で実行されてしまうのを防ぐ為に、日次・月次の専用cronは設けない）
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  kpi_count Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_KpiCount extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  kpi_count action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_KpiCount extends Pp_CliActionClass
{
    /**
     *  preprocess of kpi_count Action.
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
     *  kpi_count action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// 1時間前の集計が正常終了した事を確認する
		// TODO:未実装

		// 午前0時台は毎日の集計を実行
		$daily_flg = (date('H', $_SERVER['REQUEST_TIME']) == 0);
		
		// 毎週月曜日は週次の集計を実行
		$weekly_flg = ($daily_flg && (date('w', $_SERVER['REQUEST_TIME']) == 1));
		
		// 毎月1日は月次の集計を実行
		$monthly_flg = ($daily_flg && (date('d', $_SERVER['REQUEST_TIME']) == 1));

/*
		try {
			$this->performHourly();
			if ($daily_flg)   $this->performDaily();
			if ($monthly_flg) $this->performMonthly();
		} catch (Exception $e) {
			$this->backend->logger->log(LOG_ERR, $e->getMessage());
		}
*/
		
		try {
			if ($daily_flg)   $this->performTagDaily();
			if ($weekly_flg)  $this->performTagWeekly();
			if ($monthly_flg) $this->performTagMonthly();
		} catch (Exception $e) {
			$this->backend->logger->log(LOG_ERR, $e->getMessage());
		}
		
		return null;
    }
	
	/** 毎時の集計を実行 */
	protected function performHourly()
	{
		$admin_m = $this->backend->getManager('Admin');
		$periodlog_m = $this->backend->getManager('AdminPeriodlog');

/*
		$admin_m->makeKpiUserShop(
			Pp_AdminManager::DURATION_TYPE_HOURLY,
			$admin_m->getPreviousDurationDate(Pp_AdminManager::DURATION_TYPE_HOURLY, $_SERVER['REQUEST_TIME'])
		);
*/
		
		$periodlog_m->makeKpiPeriodUserUnique(
			$admin_m->getPreviousDurationDate(Pp_AdminManager::DURATION_TYPE_HOURLY, $_SERVER['REQUEST_TIME']),
			Pp_AdminPeriodlogManager::PERIOD_TYPE_HOURLY
		);
	}
	
	/** 毎日の集計を実行 */
	protected function performDaily()
	{
		$admin_m = $this->backend->getManager('Admin');
		$periodlog_m = $this->backend->getManager('AdminPeriodlog');
		$user_m = $this->backend->getManager('User');

/*
		$admin_m->makeKpiUserShop(
			Pp_AdminManager::DURATION_TYPE_DAILY,
			$admin_m->getPreviousDurationDate(Pp_AdminManager::DURATION_TYPE_DAILY, $_SERVER['REQUEST_TIME'])
		);
*/
			
		$yesterday = date('Y-m-d', $_SERVER['REQUEST_TIME'] - 86400);
		$admin_m->statUserBase($yesterday);

		$admin_m->makeTmpKpiMaterialNormalQuestPerUser($yesterday);
//		$admin_m->makeTmpKpiMaterialShopUsePerUser($yesterday);

		$admin_m->makeKpiUserItem();
		$admin_m->makeKpiUserMonster();

		$periodlog_m->makeKpiPeriodUserUnique(
			$admin_m->getPreviousDurationDate(Pp_AdminManager::DURATION_TYPE_DAILY, $_SERVER['REQUEST_TIME']),
			Pp_AdminPeriodlogManager::PERIOD_TYPE_DAILY
		);

		foreach (array(
			Pp_UserManager::OS_IPHONE,
			Pp_UserManager::OS_ANDROID,
		) as $ua) {
			foreach (array(
				array(0,       1000),
				array(1000,    10000),
				array(10000,   100000),
				array(100000,  1000000),
				array(1000000, 1000000000),
			) as $arr) {
				// クエスト分布
				$admin_m->makeKpiNormalQuest($yesterday, $arr[0], $arr[1], $ua);

				// ランク帯分布
				$admin_m->makeKpiRank($yesterday, $arr[0], $arr[1], $ua);
			}
		}
	}
	
	/** 毎月の集計を実行 */
	protected function performMonthly()
	{
		$admin_m = $this->backend->getManager('Admin');
		$periodlog_m = $this->backend->getManager('AdminPeriodlog');

		$month_start = $admin_m->getPreviousDurationDate(Pp_AdminManager::DURATION_TYPE_MONTHLY, $_SERVER['REQUEST_TIME']);
				
/*
		$admin_m->makeKpiUserShop(
			Pp_AdminManager::DURATION_TYPE_MONTHLY,
			$month_start
		);
*/
				
		$periodlog_m->makeKpiPeriodUserUnique(
			$month_start,
			Pp_AdminPeriodlogManager::PERIOD_TYPE_MONTHLY
		);
	}
	
	/** 毎日の集計（KPIタグ送信関連）を実行 */
	protected function performTagDaily()
	{
		$kpi_m = $this->backend->getManager('AdminKpi');

		$kpi_m->makeKpiMnPaidCoinPerTotalCirculation();
		$kpi_m->makeKpiMnPaidCoinPerCirculationOfUser28();
		$kpi_m->makeKpiMagicalmedalFreeTotalDau();
		$kpi_m->makeKpiMagicalmedalTotalDau();
	}

	/** 毎週の集計（KPIタグ送信関連）を実行 */
	protected function performTagWeekly()
	{
		$periodlog_m = $this->backend->getManager('AdminPeriodlog');

		$week_start = $periodlog_m->getDateStart(Pp_PeriodlogManager::PERIOD_TYPE_WEEKLY, $_SERVER['REQUEST_TIME'] - 86400 * 7);
				
		$periodlog_m->makeKpiMtCumulativeSales($week_start);
		$periodlog_m->makeKpiMtCumulativePaidCount($week_start);
	}

	/** 毎月の集計（KPIタグ送信関連）を実行 */
	protected function performTagMonthly()
	{
		$admin_m = $this->backend->getManager('Admin');
		$periodlog_m = $this->backend->getManager('AdminPeriodlog');
		$kpi_m = $this->backend->getManager('AdminKpi');

		$month_start = $admin_m->getPreviousDurationDate(Pp_AdminManager::DURATION_TYPE_MONTHLY, $_SERVER['REQUEST_TIME']);
		
		$kpi_m->makeKpiVrAverageFriendCount($month_start);
		$periodlog_m->makeKpiAcInstallMau($month_start);
		$periodlog_m->makeKpiMtInstallPaidMau($month_start);
		$periodlog_m->makeKpiMtInstallUserSales($month_start);
	}
}

?>