<?php
/**
 *  Admin/Api/Tar/Exit.php
 *
 *  tarコマンドのexit codeを取得する
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_api_tar_exit Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminApiTarExit extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		// ダウンロードユニーク値
        'download_uniq' => array(
            // Form definition
            'type'        => VAR_TYPE_STRING,  // Input type
            'form_type'   => FORM_TYPE_HIDDEN, // Form type
			'name'        => 'download_uniq',   // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 1,               // Minimum value
            'max'         => 256,             // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        
            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => null,            // Optional method name which
                                              // is defined in this(parent) class.
        ),
    );
}

/**
 *  admin_api_tar_exit action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminApiTarExit extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_api_tar_exit Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
		
//		if (!isset($COOKIE['download_uniq'])) {
//			return 'admin_error_400';
//		}
//		
//		$download_uniq = $COOKIE['download_uniq'];
//		$this->af->setApp('download_uniq', $download_uniq);
    }

    /**
     *  admin_api_tar_exit action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
//		$download_uniq = $this->af->getApp('download_uniq');
		$download_uniq = $this->af->get('download_uniq');
		
		$cache_key = 'exit_code_' . $download_uniq;
		
		// セッションからexit codeを取り出す
		$exit_code = $this->session->get($cache_key);

		if (!is_numeric($exit_code)) {
			return 'admin_error_500';
		}
		
		$this->af->setApp('exit_code', $exit_code);
		
        return 'admin_api_tar_exit';
    }
}

?>