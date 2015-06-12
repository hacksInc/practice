<?php
/**
 *  Admin/Program/Deploy/Diff/File.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminViewClass.php';

/**
 *  admin_program_deploy_diff_file view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminProgramDeployDiffFile extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$wcroot = $this->af->getApp('wcroot');
        
        foreach (array(
            'file1' => 'lhs_value', 
            'file2' => 'rhs_value'
        ) as $file_varname => $content_varname) {
            $file = $this->af->getApp($file_varname);
            
            if ($file) {
                $is_absolute = (strcmp($file[0], '/') === 0);
                if ($is_absolute) {
                    $fullpath = $file;
                } else {
                    $fullpath = $wcroot . '/' . $file;
                }

                $contents = file_get_contents($fullpath);
            } else {
                $contents = '';
            }
            
    		$this->af->setAppNe($content_varname, $contents);
        }
    }
}

?>