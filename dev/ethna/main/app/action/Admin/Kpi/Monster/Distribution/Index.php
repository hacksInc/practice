<?php
/**
 *  Admin/Kpi/Monster/Distribution/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_kpi_monster_distribution_* で共通のアクションフォーム定義 */
class Pp_Form_AdminKpiMonsterDistribution extends Pp_AdminActionForm
{
    /**
     * コンストラクタ
     */
    function __construct(&$backend) {
        $form_template = array(
			'monster_id' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,    // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => 'モンスターID',     // Display name

				//  Validator (executes Validator by written order.)
				'required'    => false,           // Required Option(true/false)
				'min'         => 1,               // Minimum value
				'max'         => null,            // Maximum value
			),
			'search_flg' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,    // Input type

				//  Validator (executes Validator by written order.)
				'required'    => false,           // Required Option(true/false)
				'min'         => 0,               // Minimum value
				'max'         => 1,               // Maximum value
			),
        );

        foreach ($form_template as $key => $value) {
            $this->form_template[$key] = $value;
        }
        parent::__construct($backend);
    }
	
    /**
     *  ユーザ定義検証メソッド(フォーム値間の連携チェック等)
     *
     *  @access protected
     */
    function _validatePlus()
    {
		// search_flgがONだったら他の引数は必須
		$search_flg = $this->get('search_flg');
		if ($search_flg) {
			foreach (array('monster_id') as $key) {
				if (!$this->get($key)) {
					$name = $this->form_template[$key]['name'];
					$this->ae->add(null, $name . 'を指定して下さい', E_FORM_INVALIDVALUE);
				}
			}
		}
    }
}

/**
 *  admin_kpi_monster_distribution_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiMonsterDistributionIndex extends Pp_Form_AdminKpiMonsterDistribution
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'monster_id',
        'search_flg',
    );
}

/**
 *  admin_kpi_monster_distribution_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminKpiMonsterDistributionIndex extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_kpi_monster_distribution_index Action.
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
     *  admin_kpi_monster_distribution_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('search_flg', $search_flg);
        if ($search_flg != '1'){
            return 'admin_kpi_monster_distribution_index';
        }
		$monster_m =& $this->backend->getManager('AdminMonster');
		$monster_id = $this->af->get('monster_id');
		
		$monster_master = $monster_m->getMasterMonsterAssoc();
		$monster_name = $monster_master[$monster_id]['name_ja'];
		$this->af->setApp('monster_name', $monster_name);
		
		//モンスターが存在しない
		if ($monster_name == '') {
            return 'admin_kpi_monster_distribution_index';
        }
		
		$unit_m =& $this->backend->getManager('Unit');
		$unit_all = $this->config->get('unit_all');
		$unit = $this->session->get('unit');
		$data = array();
		$total = 0;
		foreach ($unit_all as $unit_no => $unit_info) {
			$sql = "SELECT COUNT(*) as mon_cnt FROM t_user_monster where monster_id=?";
		//	$ret = $unit_m->executeForUnit($unit_no, $sql, null);
			$ret = $unit_m->getOneMultiUnit($sql, array($monster_id), $unit_no, true);
		//	error_log("unit=$unit_no:cnt=".print_r($ret, true));
		//	error_log("unit=$unit_no:cnt=$ret");
			$data[$unit_no] = $ret;
			$total += $ret;
		}
        $this->af->setApp('data', $data);
        $this->af->setApp('total', $total);
		
        return 'admin_kpi_monster_distribution_index';
    }
}
