<?php
/**
 *  Resource/Assetbundle.php
 *
 *  @author    {$author}
 *  @package   Pp
 *  @version   $Id$
 */

require_once 'Pp_ResourceActionClass.php';

/**
 *  Resource_Assetbundle form implementation
 *
 *  @author    {$author}
 *  @access    public
 *  @package   Pp
 */

class Pp_Form_ResourceAssetbundle extends Pp_ActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
	  'datab' => array(
	      // Form definition
	      'type'        => VAR_TYPE_STRING,    // Input type
	      'form_type'   => FORM_TYPE_TEXT,  // Form type
	      'name'        => 'datab',        // Display name

	      //  Validator (executes Validator by written order.)
	      'required'    => false,            // Required Option(true/false)
	      'min'         => null,            // Minimum value
	      'max'         => null,            // Maximum value
	      'regexp'      => null,            // String by Regexp
	      'mbregexp'    => null,            // Multibype string by Regexp
	      'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 

	      //  Filter
	      'filter'      => 'hex_base64_decrypt',        // Optional Input filter to convert input
	      'custom'      => null,            // Optional method name which
	                                        // is defined in this(parent) class.
	  ),
	);
}

/**
 *  Resource_Assetbundle action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ResourceAssetbundle extends Pp_ResourceActionClass
{
	/**
	 *  preprocess Resource_Assetbundle action.
	 *
	 *  @access    public
	 *  @return    string  Forward name (null if no errors.)
	 */
	function prepare()
	{
		$this->logger->log(LOG_DEBUG, "call prepare().");
		
		// データ取得
		try {
			if ($this->af->validate() > 0) {
				$this->logger->log(LOG_INFO, "error_400.");
				return 'error_400';
			}
			
		} catch (Exception $e) {
			
			// エラーログ出力
			$this->logger->log(LOG_INFO, "Exception: ".$e->getMessage());
			
			return 'error_401';
		}
		
		return null;
	}

	/**
	 *  Assetbundle action implementation.
	 *
	 *  @access    public
	 *  @return    string  Forward Name.
	 */
	function perform()
	{
		$this->logger->log(LOG_DEBUG, "call perform().");
		$date = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		
		$datab = $this->af->get('datab');
		$this->logger->log(LOG_DEBUG, "datab=".$datab.".");
		
		$json_data = json_decode($datab, true);
		
		if ( empty($json_data) ) {
			$this->logger->log(LOG_INFO, "json_data is empty.");
			header("HTTP/1.0 406 Not Acceptable");
			return;
		}
		$this->logger->log(LOG_DEBUG, "json_data=(".implode(", ", $json_data).")");
		
		$user_id = null;
		if ( isset($json_data['user_id']) ) {
			$user_id = $json_data['user_id'];
		}
		
		$dir = null;
		if ( isset($json_data['dir']) ) {
			$dir = $json_data['dir'];
		}
		
		$file_name = null;
		if ( isset($json_data['file_name']) ) {
			$file_name = $json_data['file_name'];
		}
		
		$device_name = null;
		if ( isset($json_data['device_name']) ) {
			// PC:"",Android:"Android",iPhone:"iPhone"
			$device_name = $json_data['device_name'];
		}
		
		$this->logger->log(LOG_DEBUG, "user_id=(".$user_id."), dir=(".$dir."), file_name=(".$file_name."), device_name=(".$device_name.").");
		
		// 設定チェック
		$asset_bundle_m =& $this->backend->getManager('Assetbundle');
		$asset_bundle_data = $asset_bundle_m->getAssetBundle($date, $dir, $file_name);
		if ( empty($asset_bundle_data) ) {
			$this->logger->log(LOG_INFO, "Data Not Found. user_id=(".$user_id."), date=(".$date."), dir=(".$dir."), file_name=(".$file_name.").");
			header("HTTP/1.0 406 Not Acceptable");
			return;
		}
		// 最新ヴァージョンのを対象にする
		usort($asset_bundle_data,  array($this, 'sort_by_version'));
		$asset_bundle_part = array_pop($asset_bundle_data);
		$version = $asset_bundle_part['version'];
		$id      = $asset_bundle_part['id'];
		$extension = $asset_bundle_part['extension'];
		
		// ディレクトリチェック
		$dir_path = BASE . "/data/resource/assetbundle/";
		if (!empty($dir)) {
			$dir_path .= $dir."/";
		}
		if(!is_dir($dir_path)){
			$this->logger->log(LOG_INFO, "Directory Not Found. dir_path=($dir_path).");
			header("HTTP/1.0 403 Forbidden");
			return;
		}
		
		// ファイル名生成
		$joint_file_name = $file_name.".".$version;
		if (!empty($device_name)) {
			$joint_file_name .= ".".$device_name;
		}
		$joint_file_name .= '.'.$extension;
		
		// ファイルチェック
		$file_path = $dir_path.$joint_file_name;
		if(!is_file($file_path)){
			$this->logger->log(LOG_INFO, "File Not Found. file_path=($file_path).");
			header("HTTP/1.0 404 Not Found");
			return;
		}
		$this->logger->log(LOG_DEBUG, "Response File. file_path=($file_path).");
		
		header('Content-type: application/octet-stream');
		header('Content-Length: ' . fileSize($file_path));
		header('Last-Modified: ' . gmdate ('D, d M Y H:i:s', filemtime($file_path)) . ' GMT');
		header('Cache-Control: max-age=300');
		header('Content-Disposition: attachment; filename="'.$file_name.'"');
		
		readfile( $file_path );
		
		exit;
	}
	
	function sort_by_version($a, $b) {
		// version小さい順
		return $a['version'] > $b['version'];
	}
	
}

?>
