<?php
/**
 *  Admin/Kpi/Monster/Use/Createfile.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Index.php';

/**
 *  admin_kpi_monster_use_createfile Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiMonsterUseCreatefile extends Pp_Form_AdminKpiMonsterUse
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'search_date_from' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'search_date_to' => array(
            'filter'      => 'urldecode',        // Optional Input filter to convert input
        ),
        'monster_id',
        'search_flg',
    );
}

/**
 *  admin_kpi_monster_use_createfile action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminKpiMonsterUseCreatefile extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_kpi_monster_use_createfile Action.
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
     *  admin_kpi_monster_use_createfile action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $kpiview_m = $this->backend->getManager('KpiViewMonster');

        $date_from  = $this->af->get('search_date_from') . ' 00:00:00';
        $date_to    = $this->af->get('search_date_to') . ' 00:00:00';
        $monster_id = $this->af->get('monster_id');

        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('search_flg', $search_flg);
        if ($search_flg != '1'){
            $this->af->setApp('code', 100);
            return 'admin_json_encrypt';
        }

		$kpi_monster_master = $kpiview_m->getKpiMonsterMasterTable($monster_id, $date_from);
		$kpi_monster_use = $kpiview_m->getKpiMonsterUseTable($monster_id, $date_from, $date_to);
		
        if (count($kpi_monster_use) == 0) {
             $this->af->setApp('code', 400);
             $this->af->setApp('err_msg', "対象となるデータが存在しません。");
             return 'admin_json_encrypt';
        }
		
        $res = $kpiview_m->createCsvFileKpiMonsterUse($kpi_monster_use, $kpi_monster_master);
        if ($res === false){
            $this->af->setApp('code', 400);
            $this->af->setApp('err_msg', 'ファイルの作成に失敗しました。');
            return 'admin_json_encrypt';
        }

        $this->af->setApp('code', 200);
        $this->af->setApp('file_name', $res);
        return 'admin_json_encrypt';
    }
}
