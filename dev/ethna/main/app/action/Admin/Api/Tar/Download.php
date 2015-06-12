<?php
/**
 *  Admin/Api/Tar/Download.php
 *
 *  tarで所定ディレクトリをダウンロードする
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_api_tar_download Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminApiTarDownload extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'target' => array(
            'type'        => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => true,                 // Required Option(true/false)
            'min'         => null,                 // Minimum value
            'max'         => 16,                   // Maximum value
            'regexp'      => '/^program|assetbundle|announce$/', // String by Regexp
        ),
    );
}

/**
 *  admin_api_tar_download action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminApiTarDownload extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_api_tar_download Action.
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
     *  admin_api_tar_download action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$admin_m =& $this->backend->getManager('Admin');
		
		$target = $this->af->get('target');
		
		$subdirs = $admin_m->DIRECTORIES[$target];
		
		$root = dirname(BASE);
		$base = basename(BASE);
		
		// コマンド組み立て
		if ($target == 'assetbundle') { // アセットバンドルの場合
			if (count($subdirs) != 1) {
				return 'admin_error_500';
			}

			// 圧縮ファイル内の第1階層にassetbundleディレクトリがあるようにする
			// （サブディレクトリを設けない）
			$root2 = BASE . '/' . dirname($subdirs[0]);
			$subdir2 = basename($subdirs[0]);
			
			$command = "tar cz --directory=$root2 --exclude='*.gz' --exclude='*.tgz' --exclude='*.bz2' --exclude='*.bak' --exclude='.svn'"
			         . " ./$subdir2";
			
		} else {
			$command = "tar cz --directory=$root --exclude='*.gz' --exclude='*.tgz' --exclude='*.bz2' --exclude='*.bak' --exclude='.svn'";
			foreach ($subdirs as $subdir) {
				$command .= " ./$base/$subdir";
			}
		}

		// ファイル名生成
		$filename = $base . "_" . Util::getEnv() . '_' . $target . '_' . date("YmdHi", $_SERVER['REQUEST_TIME']) . ".tgz";
		
		// ダウンロードユニーク値を生成
		$download_uniq = uniqid();

		$this->af->setApp('command',       $command);
		$this->af->setApp('filename',      $filename);
		$this->af->setApp('download_uniq', $download_uniq);
		
        return 'admin_api_tar_download';
    }
}

?>