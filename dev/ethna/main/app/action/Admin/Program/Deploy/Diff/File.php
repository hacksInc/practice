<?php
/**
 *  Admin/Program/Deploy/Diff/File.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_program_deploy_diff_file Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminProgramDeployDiffFile extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
 		'msg' => array(
            'type'        => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 1,               // Minimum value
            'max'         => 1024,            // Maximum value
        ),
		
		'checkout_uniq' => array(
			// Form definition
            'type'        => VAR_TYPE_STRING, // Input type

			//  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 64,              // Maximum value
		),
        
		'wcside' => array(
            'type'        => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => true,               // Required Option(true/false)
            'min'         => null,               // Minimum value
            'max'         => 5,                  // Maximum value
            'regexp'      => '/^left$|^right$/', // String by Regexp
        ),
    );
}

/**
 *  admin_program_deploy_diff_file action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminProgramDeployDiffFile extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_program_deploy_diff_file Action.
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
		
		$msg = $this->af->get('msg');
		$wcside = $this->af->get('wcside');
		
		if (preg_match('/^Files .+ and .+ differ$/', $msg)) {
//			$type = 'differ';
			
			strtok($msg, ' ');    // Files
			$file1 = strtok(' '); // .+
			strtok(' ');          // and
			$file2 = strtok(' '); // .+
		} else if (preg_match('/^Only in .+: .+/', $msg)) {
//			$type = 'only';
			
			strtok($msg, ' ');   // Only
			strtok(' ');         // in
			$dir = strtok(': '); // .+:
			$file = strtok(' '); // .+
            
            $file_tmp = "$dir/$file";
            $is_absolute = (strcmp($dir[0], '/') === 0);
            if ((($wcside == 'left') && !$is_absolute) ||
                (($wcside == 'right') && $is_absolute)
            ) {
   				$file1 =$file_tmp;
       			$file2 = null;
            } else {
   				$file1 = null;
       			$file2 = $file_tmp;
            }
		} else {
			$this->backend->logger->log(LOG_INFO, 'Invalid msg.');
			return 'admin_error_400';
		}
		
//		$this->af->setApp('type',  $type);
		$this->af->setApp('file1', $file1);
		$this->af->setApp('file2', $file2);
    }
	
    /**
     *  admin_program_deploy_diff_file action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$developer_m =& $this->backend->getManager('Developer');
		
		$checkout_uniq = $this->af->get('checkout_uniq');
		
		$wcroot = $developer_m->getTmpSvnWcroot($checkout_uniq, session_id());
		
        foreach (array('file1', 'file2') as $varname) {
            $file = $this->af->getApp($varname);
            if (!$file) {
                continue;
            }
            
            $is_absolute = (strcmp($file[0], '/') === 0);
            if ($is_absolute) {
    			$this->checkFile($file);
            } else {
    			$this->checkFile($file, $wcroot);
            }
        }

		if ($this->ae->count() > 0) {
	        return 'admin_error_500';
		}
		
		$this->af->setApp('wcroot', $wcroot);
		
        return 'admin_program_deploy_diff_file';
    }
	
	protected function checkFile($file, $wcroot = null)
	{
		if ($wcroot) {
			$fullpath = $wcroot . "/" . $file;
		} else {
			$fullpath = $file;
		}
		
		$base_slash = BASE . '/';
		
		if (strcmp($fullpath[0], '/') !== 0) {
			$this->ae->add(null, "ファイル名が不正です");
		} else if (strpos($fullpath, '..') !== false) {
			$this->ae->add(null, "ファイル名が不正です");
		} else if (strncmp($fullpath, $base_slash, strlen($base_slash)) !== 0) {
			$this->ae->add(null, "ファイル名が不正です");
		} else if (is_dir($fullpath)) {
			$this->ae->add(null, $file . "はディレクトリです");
		} else if (!file_exists($fullpath)) {
			$this->ae->add(null, $file . "が見つかりません");
		} else if (!is_file($fullpath)) {
			$this->ae->add(null, $file . "はファイルではありません");
		}
	}
}

?>