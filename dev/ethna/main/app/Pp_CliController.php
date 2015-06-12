<?php
/**
 *  Pp_CliController.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_Controller.php';

/**
 *  Pp CLI application Controller definition.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_CliController extends Pp_Controller
{
	/**
	 *  @var    array   class definition.
	 */
	var $class = array(
		/*
		 *  TODO: When you override Configuration class, Logger class,
		 *        SQL class, don't forget to change definition as follows!
		 */
		'class'         => 'Ethna_ClassFactory',
//		'backend'       => 'Pp_Backend',
		'backend'       => 'Pp_CliBackend',
//		'config'        => 'Ethna_Config',
		'config'        => 'Pp_CliConfig',
		'db'            => 'Pp_DB_ADOdb',
		'error'         => 'Ethna_ActionError',
		'form'          => 'Pp_ActionForm',
		'i18n'          => 'Ethna_I18N',
//		'logger'        => 'Pp_Logger',
		'logger'        => 'Pp_CliLogger',
		'plugin'        => 'Ethna_Plugin',
		'session'       => 'Ethna_Session',
		'sql'           => 'Ethna_AppSQL',
		'view'          => 'Pp_ViewClass',
		'renderer'      => 'Ethna_Renderer_Smarty',
		'url_handler'   => 'Pp_UrlHandler',
	);
}
