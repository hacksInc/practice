<?php

class Member extends AppModel {

	public $name = 'Member';

	public $hasAndBelongsToMany = array(
		'Project' => array(
			'className' => 'Project',
			'joinTable' => 'members_projects',
			'foreignKey' => 'member_id',
			'associationForeignKey' => 'project_id',
			'with' => 'MembersProject',
			'unique' => true,
		),
	);

	public $belongsTo = array(
		'File' => array(
			'className' => 'File',
			'foreignKey' => 'file_id'
		)
	);


	public $validate = array(
		'sei' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 10),
				'message' => 'この項目は10文字までです',
				'last' => false
			),
			'textCheck' => array(
				'rule' => '/^[ぁ-んァ-ンa-zA-Z一-龥]+$/u',
				'message' => 'この項目は日本語もしくは英語のみです'
			)
		),
		'mei' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 10),
				'message' => 'この項目は10文字までです',
				'last' => false
			),
			'textCheck' => array(
				'rule' => '/^[ぁ-んァ-ヶーa-zA-Z一-龠]+$/u',
				'message' => 'この項目は日本語もしくは英語のみです'
			)
		),
		'sei_kana' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 10),
				'message' => 'この項目は10文字までです',
				'last' => false
			),
			'textCheck' => array(
				'rule' => '/^[ァ-ヶーa-zA-Z]+$/u',
				'message' => 'この項目はカタカナもしくは英語のみです'
			)
		),
		'mei_kana' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 10),
				'message' => 'この項目は10文字までです',
				'last' => false
			),
			'textCheck' => array(
				'rule' => '/^[ァ-ヶーa-zA-Z]+$/u',
				'message' => 'この項目はカタカナもしくは英語のみです'
			)
		),
		'year' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
		),
		'month' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
		),
		'day' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
		),
		'email' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 120),
				'message' => 'この項目は120文字までです',
				'last' => false
			),
			'email' => array(
				'rule' => array('email', true),
				'message' => '有効なメールアドレスを入力してください'
			),
		),
		'tel' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 15),
				'message' => 'この項目は15文字までです',
				'last' => false
			),
			'textCheck' => array(
				'rule' => '/^[0-9+-]+$/',
				'message' => 'この項目は数字のみです'
			)
		),
		'station' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 25),
				'message' => 'この項目は25文字までです',
				'last' => false
			),
			'textCheck' => array(
				'rule' => '/^[ぁ-んァ-ヶーa-zA-Z0-9一-龠]+$/u',
				'message' => 'この項目は日本語もしくは英数字のみです'
			)
		),
		'File' => array(
			'fileSize' => array(
				'rule' => array('fileSize', '<=', '8MB'),
				'message' => 'ファイルは8MB以下までです。これ以上のファイルは後ほどEmailにてお願い致します。',
				'allowEmpty' => true
			),
		),
		'have_skill' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 1000),
				'message' => 'この項目は1000文字までです',
				'allowEmpty' => true
			),
		),
		'hope' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 1000),
				'message' => 'この項目は1000文字までです',
				'allowEmpty' => true
			),
		),
		'other' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 1000),
				'message' => 'この項目は15文字までです',
				'allowEmpty' => true
			),
		),
	);
	
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