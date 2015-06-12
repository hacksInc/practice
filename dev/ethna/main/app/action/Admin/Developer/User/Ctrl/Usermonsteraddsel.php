<?php
/**
 *  Admin/Developer/User/Ctrl/Usermonsteraddsel.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_ctrl_usermonsteraddsel Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperUserCtrlUsermonsteraddsel extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'id' => array(
            // Form definition
			// VAR_TYPE_INTにすると空文字列を渡されたときにエラーになるので文字列として扱う
            'type'        => VAR_TYPE_STRING, // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'id',            // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => '/[0-9]*/',      // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        ),
        
		'monster_list',
    );

    /**
     *  Form input value convert filter : sample
     *
     *  @access protected
     *  @param  mixed   $value  Form Input Value
     *  @return mixed           Converted result.
     */
    /*
    function _filter_sample($value)
    {
        //  convert to upper case.
        return strtoupper($value);
    }
    */
}

/**
 *  admin_developer_user_ctrl_usermonsteraddsel action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperUserCtrlUsermonsteraddsel extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_user_edit Action.
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
     *  admin_developer_user_edit action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$developer_m =& $this->backend->getManager('Developer');
		
		$user_m =& $this->backend->getManager('User');
		$monster_m =& $this->backend->getManager('AdminMonster');
		
		$user_id = $this->af->get('id');
		$user_base = $user_m->getUserBaseForApiResponse($user_id);
		
		$monster_list = $this->af->get('monster_list');
		$monsters = explode("\r\n", $monster_list);
		$monster_all = $monster_m->getMasterMonsterAssoc();
		$monster_add = array();
		$monster_err = array();
		foreach($monsters as $key => $val) {
			if (array_key_exists($val, $monster_all) ) {
				$ret = $monster_m->createUserMonster($user_id, $val, null, null, false);
				//モンスター図鑑を更新
				$ret = $monster_m->setUserMonsterBookVar($user_id, $val, Pp_MonsterManager::BOOK_STATUS_GOT);
				$monster_add[] = array('id' => $val, 'name' => $monster_all[$val]['name_ja']);
			} else {
				$monster_err[] = $val;
			}
		}
		//モンスター図鑑を保存
		$ret = $monster_m->saveUserMonsterBookBits($user_id);
		$monster_list = $monster_m->getUserMonsterListForApiResponseAd($user_id);
		
		$this->af->setApp('base',           $user_base);
		$this->af->setApp('monster_cnt',    count($monster_list));
		
		$this->af->setApp('monster_add',    $monster_add);
		$this->af->setApp('monster_add_cnt',count($monster_add));
		$this->af->setApp('monster_err',    $monster_err);
		$this->af->setApp('monster_err_cnt',count($monster_err));
		
        return 'admin_developer_user_ctrl_usermonsteraddsel';
    }
}

?>