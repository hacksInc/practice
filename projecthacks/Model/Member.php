<?php

class Member extends AppModel {

	/* もしテーブルを使用しない場合
	 * public $useTable = false;
	 * public $_schema = array(
	 *		'name' => array('type' => 'string', 'length' => '255')
	 * );
	 */

	public $name = 'Member';
	public $belongsTo = array(
		'File' => array(
			'className' => 'File',
			'foreignKey' => 'file_id'
		)
	);
/*
	public $validate = array(
		'sei' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => array('未入力です。'),
				'required' => true,
			),
			'maxLength' => array(
				'rule' => array('maxLnegth' => 255),
				'message' => array('255文字以内で入力してください。'),
				'required' => true,
			),
		),
	);
*/
	
	public function year() {

		$old = DATE('Y') - 60;
		$young = DATE('Y') - 16;
		$year = array();
		for ($i=$old; $i<$young; $i++){
			$year[$i] = $i;
		}
		return $year;
	}
	public function month() {
		$month = array();
		for ( $i = 1; $i < 13; $i++) {
			$month[$i] = $i;
		}
		return $month;
	}
	public function day() {
		$day = array();
		for ( $i = 1; $i < 32; $i++) {
			$day[$i] = $i;
		}
		return $day;
	}

}