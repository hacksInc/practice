<?php
/**
 *	admin_program_entry_* で共通のアクションフォーム定義
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */
class Pp_Form_AdminProgramEntry extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'current_ver' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => '現行バージョン', // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => true,			  // Required Option(true/false)
				'min'		  => 1, 			  // Minimum value
				'max'		  => null,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
				//	Filter
				'filter'	  => null,			  // Optional Input filter to convert input
				'custom'	  => null,			  // Optional method name which
												  // is defined in this(parent) class.
			),
			
			'review_ver' => array(
				// Form definition
				'type'		  => VAR_TYPE_INT,	  // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'		  => 'レビューバージョン', // Display name

				//	Validator (executes Validator by written order.)
				'required'	  => false,	          // Required Option(true/false)
				'min'		  => 1, 			  // Minimum value
				'max'		  => null,			  // Maximum value
				'regexp'	  => null,			  // String by Regexp
				'mbregexp'	  => null,			  // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
				//	Filter
				'filter'	  => null,			  // Optional Input filter to convert input
				'custom'	  => null,			  // Optional method name which
												  // is defined in this(parent) class.
			),
		);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}
		
		parent::__construct($backend);
	}
}

