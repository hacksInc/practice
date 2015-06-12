<?php
/**
 *  Admin/Login.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_login Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogin extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'lid' => array(
            // Form definition
            'type'        => VAR_TYPE_STRING, // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'lid',           // Display name
          
            //  Validator (executes Validator by written order.)
            'required'    => false,            // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => 'sample',        // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
        'lpw' => array(
            // Form definition
            'type'        => VAR_TYPE_STRING, // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'lpw',           // Display name
          
            //  Validator (executes Validator by written order.)
            'required'    => false,            // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => 'sample',        // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
        'loginpath' => array(
            // Form definition
            'type'        => VAR_TYPE_STRING, // Input type
          
            //  Validator (executes Validator by written order.)
            'required'    => false,           // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => 256,             // Maximum value
            'regexp'      => '/^[a-zA-Z0-9_\/?&=]+$/', // String by Regexp
        ),
		'unit' => array(
			// Form definition
			'type'        => VAR_TYPE_INT,     // Input type
			'form_type'   => FORM_TYPE_SELECT, // Form type

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 999,             // Maximum value
		),
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
 *  admin_login action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogin extends Pp_AdminActionClass
{
	protected $must_login = false;
	
 	protected $must_permission = false;
	
   /**
     *  preprocess of admin_login Action.
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
     *  admin_login action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		if (!$this->session->isStart()) {
			$request_id = $this->af->get('lid');
			$request_pw = $this->af->get('lpw');
			$unit       = $this->af->get('unit');
			
			if ($request_id && $request_pw) {
				$admin_m =& $this->backend->getManager('Admin');

				$fail_cnt = $admin_m->accessAuthFailCnt($request_id);
				if ($fail_cnt >= 3) {
					$this->ae->add(null, "このIDは現在ロックされています。ロックは一定時間で自動的に解除されます。");
				} else if ($admin_m->isValidAdminPassword($request_id, $request_pw)) {
					// セッション開始
					$this->session->start();
					$this->session->set('lid', $request_id);
					$this->session->set('unit', $unit);
					
					// リダイレクト先を判定
					$path = $this->af->get('loginpath');
					if ((strlen($path) == 0) || ($path == '/psychopass_game/admin/login')) {
						$path = '/psychopass_game/admin/index';
					}

					$this->af->setApp('path', $path);
					return 'admin_redirect';
				} else {
					$this->ae->add(null, "IDもしくはPasswordが正しくありません｡");
					$admin_m->accessAuthFailCnt($request_id, 1);
				}
			} else {
				$this->ae->add(null, "IDまたはPasswordを入力して下さい。");
			}
		} else {
			return 'admin_index';
		}

		return 'admin_login';
    }
}

?>
