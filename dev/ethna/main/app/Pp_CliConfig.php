<?php
/**
 *  Pp_CliConfig.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  CLI設定クラス
 *
 *  @access     public
 *  @package    Pp
 */
class Pp_CliConfig extends Ethna_Config
{
    /**
     *  設定ファイル名を取得する
     *
     *  @access private
     *  @return string  設定ファイルへのフルパス名
     */
    function _getConfigFile()
    {
        return $this->controller->getDirectory('etc') . '/' . strtolower($this->controller->getAppId()) . '-ini-cli.php';
    }
}
