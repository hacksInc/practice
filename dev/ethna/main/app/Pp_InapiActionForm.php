<?php
// vim: foldmethod=marker
/**
 *  Pp_InapiActionForm.php
 *
 *  内部ネットワークAPIアクションフォーム
 *  Node.jsからのリクエスト受付用
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

// {{{ Pp_InapiActionForm
/**
 *  InapiActionForm class.
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_InapiActionForm extends Ethna_ActionForm
{
	/**#@+
	 *  @access private
	 */

	/** @var    array   form definition (default) */
	var $form_template = array();

	/**#@-*/

	protected $output_names_no_convert = array();
	
	/**
	 *  Error handling of form input validation.
	 *
	 *  @access public
	 *  @param  string      $name   form item name.
	 *  @param  int         $code   error code.
	 */
	function handleError($name, $code)
	{
		return parent::handleError($name, $code);
	}

	/**
	 *  setter method for form template.
	 *
	 *  @access protected
	 *  @param  array   $form_template  form template
	 *  @return array   form template after setting.
	 */
	function _setFormTemplate($form_template)
	{
		return parent::_setFormTemplate($form_template);
	}

	/**
	 *  setter method for form definition.
	 *
	 *  @access protected
	 */
	function _setFormDef()
	{
		return parent::_setFormDef();
	}

	/**
	 *  Form input value convert filter : hex_base64_decrypt
	 *
	 *  @access protected
	 *  @param  mixed   $value  Form Input Value
	 *  @return mixed           Converted result.
	 */
	function _filter_hex_base64_decrypt($value)
	{
		$value_byte = pack('H*', $value);
		// Base64を解読する
		$value_str = base64_decode($value_byte);
		
		$this->logger->log(LOG_DEBUG, "base64_decode value=(".print_r($value_str, true).").");
		
		return $value_str;
	}
	
    /**
     *  ユーザから送信されたフォーム値をフォーム値定義に従ってインポートする
     *
	 *  Ethna_ActionFormの同名関数を基に作成
     *  @access public
     */
    function setFormVars()
    {
//        if (isset($_SERVER['REQUEST_METHOD']) == false) {
//            return;
//        } else if (strcasecmp($_SERVER['REQUEST_METHOD'], 'post') == 0) {
//            $http_vars =& $_POST;
//        } else {
//            $http_vars =& $_GET;
//        }

		$json = file_get_contents('php://input');
		$this->logger->log(LOG_INFO, 'Input Json:' . $json);
		$http_vars = json_decode($json, true);

        //
        //  ethna_fid というフォーム値は、フォーム定義に関わらず受け入れる
        //  これは、submitされたフォームを識別するために使われる
        //  null の場合は、以下の場合である
        //
        //  1. フォームヘルパが使われていない
        //  2. 画面の初期表示などで、submitされなかった
        //  3. {form name=...} が未設定である
        //
//        $this->form_vars['ethna_fid'] = (isset($http_vars['ethna_fid']) == false
//                                      || is_null($http_vars['ethna_fid']))
//                                      ? null
//                                      : $http_vars['ethna_fid'];

        foreach ($this->form as $name => $def) {
            $type = is_array($def['type']) ? $def['type'][0] : $def['type'];
            if ($type == VAR_TYPE_FILE) {
//                // ファイルの場合
//
//                // 値の有無の検査
//                if (is_null($this->_getFilesInfoByFormName($_FILES, $name, 'tmp_name'))) {
//                    $this->set($name, null);
//                    continue;
//                }
//
//                // 配列構造の検査
//                if (is_array($def['type'])) {
//                    if (is_array($this->_getFilesInfoByFormName($_FILES, $name, 'tmp_name')) == false) {
//                        $this->handleError($name, E_FORM_WRONGTYPE_ARRAY);
//                        $this->set($name, null);
//                        continue;
//                    }
//                } else {
//                    if (is_array($this->_getFilesInfoByFormName($_FILES, $name, 'tmp_name'))) {
//                        $this->handleError($name, E_FORM_WRONGTYPE_SCALAR);
//                        $this->set($name, null);
//                        continue;
//                    }
//                }
//
//                $files = null;
//                if (is_array($def['type'])) {
//                    $files = array();
//                    // ファイルデータを再構成
//                    foreach (array_keys($this->_getFilesInfoByFormName($_FILES, $name, 'name')) as $key) {
//                        $files[$key] = array();
//                        $files[$key]['name'] = $this->_getFilesInfoByFormName($_FILES, $name."[".$key."]", 'name');
//                        $files[$key]['type'] = $this->_getFilesInfoByFormName($_FILES, $name."[".$key."]", 'type');
//                        $files[$key]['size'] = $this->_getFilesInfoByFormName($_FILES, $name."[".$key."]", 'size');
//                        $files[$key]['tmp_name'] = $this->_getFilesInfoByFormName($_FILES, $name."[".$key."]", 'tmp_name');
//                        if ($this->_getFilesInfoByFormName($_FILES, $name."[".$key."]", 'error') == null) {
//                            // PHP 4.2.0 以前
//                            $files[$key]['error'] = 0;
//                        } else {
//                            $files[$key]['error'] = $this->_getFilesInfoByFormName($_FILES, $name."[".$key."]", 'error');
//                        }
//                    }
//                } else {
//                    $files['name'] = $this->_getFilesInfoByFormName($_FILES, $name, 'name');
//                    $files['type'] = $this->_getFilesInfoByFormName($_FILES, $name, 'type');
//                    $files['size'] = $this->_getFilesInfoByFormName($_FILES, $name, 'size');
//                    $files['tmp_name'] = $this->_getFilesInfoByFormName($_FILES, $name, 'tmp_name');
//                    if ($this->_getFilesInfoByFormName($_FILES, $name, 'error') == null) {
//                        // PHP 4.2.0 以前
//                        $files['error'] = 0;
//                    } else {
//                        $files['error'] = $this->_getFilesInfoByFormName($_FILES, $name, 'error');
//                    }
//                }
//
//                // 値のインポート
//                $this->set($name, $files);

            } else {
                // ファイル以外の場合

                $target_var = $this->_getVarsByFormName($http_vars, $name);

                // 値の有無の検査
                if (isset($target_var) == false
                    || is_null($target_var)) {
                    $this->set($name, null);
                    if (isset($http_vars["{$name}_x"])
                     && isset($http_vars["{$name}_y"])) {
                        // 以前の仕様に合わせる
                        $this->set($name, $http_vars["{$name}_x"]);
                    }
                    continue;
                }

                // 配列構造の検査
                if (is_array($def['type'])) {
                    if (is_array($target_var) == false) {
                        // 厳密には、この配列の各要素はスカラーであるべき
                        $this->handleError($name, E_FORM_WRONGTYPE_ARRAY);
                        $this->set($name, null);
                        continue;
                    }
                } else {
                    if (is_array($target_var)) {
                        $this->handleError($name, E_FORM_WRONGTYPE_SCALAR);
                        $this->set($name, null);
                        continue;
                    }
                }

                // 値のインポート
                $this->set($name, $target_var);
            }
        }
    }
	
	/**
	 * 
	 * @param type $name
	 * @param type $value
	 * @param bool $output ビューでの暗号化JSON出力対象に含めるか
	 * @return type
	 */
	function setApp($name, $value, $output = false)
	{
		if ($output) {
			if (!in_array($name, $this->output_names_no_convert)) {
				$this->output_names_no_convert[] = $name;
			}
		}
		
		return parent::setApp($name, $value);
	}
	
	function getOutputNamesNoConvert()
	{
		return $this->output_names_no_convert;
	}
	
}
// }}}

?>
