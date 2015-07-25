<?php

class Contact extends AppModel {

	public $useTable = false;
	public $_schema = array(
		'sei' => array(
			'type' => 'string',
			'length' => '20',
		),
		'mei' => array(
			'type' => 'string',
			'length' => '20'
		),
		'sei_kana' => array(
			'type' => 'string',
			'length' => '20'
		),
		'mei_kana' => array(
			'type' => 'string',
			'length' => '20'
		),
		'company' => array(
			'type' => 'string',
			'length' => '255',
		),
		'company_kana' => array(
			'type' => 'string',
			'length' => '255'
		),
		'email' => array(
			'type' => 'string',
			'length' => '255'
		),
		'tel' => array(
			'type' => 'string',
			'length' => '20'
		),
		'url' => array(
			'type' => 'string',
			'length' => '255'
		),
		'content' => array(
			'type' => 'text',
		),
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
		'company' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 100),
				'message' => 'この項目は100文字までです',
				'last' => false
			),
			'textCheck' => array(
				'rule' => '/^[ぁ-んァ-ヶーa-zA-Z一-龠\\\.\/@¥_-]+$/u',
				'message' => 'この項目は日本語もしくは英語のみです'
			)
		),
		'company_kana' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 100),
				'message' => 'この項目は100文字までです',
				'last' => false
			),
			'textCheck' => array(
				'rule' => '/^[ァ-ヶーa-zA-Z\\\.\/@¥_-]+$/u',
				'message' => 'この項目はカタカナもしくは英語のみです'
			)
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
		'url' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'この項目は必須です',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 100),
				'message' => 'この項目は100文字までです',
				'last' => false
			),
			'urlCheck' => array(
				'rule' => array('url', true),
				'message' => '有効なURLを入力してください'
			)
		),
		'content' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 1000),
				'message' => 'この項目は1000文字までです',
				'allowEmpty' => true
			),
		),
	);
}