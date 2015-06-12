<?php
/**
 *  Admin/Kpi/Monster/Use/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_kpi_monster_use_* で共通のアクションフォーム定義 */
class Pp_Form_AdminKpiMonsterUse extends Pp_AdminActionForm
{
    /**
     * コンストラクタ
     */
    function __construct(&$backend) {
        $form_template = array(
            'search_date_from' => array(
                // Form definition
                'type'        => VAR_TYPE_DATETIME,     // Input type
                'form_type'   => FORM_TYPE_TEXT, // Form type
                'name'        => '検索日(開始日)', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,            // Required Option(true/false)
                //'min'         => 10000,               // Minimum value
                //'max'         => 20000,               // Maximum value
            ),
            'search_date_to' => array(
                // Form definition
                'type'        => VAR_TYPE_DATETIME,     // Input type
                'form_type'   => FORM_TYPE_TEXT, // Form type
                'name'        => '検索日(終了日)', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,            // Required Option(true/false)
                //'min'         => 10000,               // Minimum value
                //'max'         => 20000,               // Maximum value
            ),
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
			foreach (array('search_date_from', 'search_date_to', 'monster_id') as $key) {
				if (!$this->get($key)) {
					$name = $this->form_template[$key]['name'];
					$this->ae->add(null, $name . 'を指定して下さい', E_FORM_INVALIDVALUE);
				}
			}
		}
    }
}

/**
 *  admin_kpi_monster_use_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiMonsterUseIndex extends Pp_Form_AdminKpiMonsterUse
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'search_date_from',
        'search_date_to',
        'monster_id',
        'search_flg',
    );
}

/**
 *  admin_kpi_monster_use_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminKpiMonsterUseIndex extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_kpi_monster_use_index Action.
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
     *  admin_kpi_monster_use_index action implementation.
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
            return 'admin_kpi_monster_use_index';
        }

		$kpi_monster_master = $kpiview_m->getKpiMonsterMasterTable($monster_id, $date_from);
		$kpi_monster_use = $kpiview_m->getKpiMonsterUseTable($monster_id, $date_from, $date_to);

        $this->af->setApp('kpi_monster_master', $kpi_monster_master);
        $this->af->setApp('kpi_monster_use',    $kpi_monster_use);
        $this->af->setApp('kpi_date_from', date('Y年m月d日', strtotime($date_from)));
        $this->af->setApp('kpi_date_to', date('Y年m月d日', strtotime($date_to)));
		
        return 'admin_kpi_monster_use_index';
    }
}
