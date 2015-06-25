<?php
/**
 *  admin_developer_master_sync_* で共通のアクションフォーム定義
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */
class Pp_Form_AdminDeveloperMasterSync extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'algorithms' => array(
				// Form definition
				'type'      => VAR_TYPE_STRING, // Input type
				'form_type' => FORM_TYPE_RADIO, // Form type
				'name'      => '動作モード',    // Display name

				//  Validator (executes Validator by written order.)
				'required'  => false,             // Required Option(true/false)
				'option'    => array(
					'default' => '標準',
					'Stream'  => '詳細',
				),
			),
		);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}
		
		parent::__construct($backend);
	}
}
