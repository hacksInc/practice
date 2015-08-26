<?php
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel {
	public $validate = array(
		'username' => array(
			'rule' => 'notEmpty',
			'message' => 'ユーザ名を入力してください'),
		'password' => array(
			'rule' => 'notEmpty',
			'message' => 'パスワードを入力してください'),
		'role' => array(
			'valid' => array(
				'rule' => array('inlist', array('admin', 'author')),
				'message' => '権限を選択してください',
				'allowEmpty' => false))
				
		);
	public function beforeSave($options = array()) {
		if (isset($this->data['User']['password'])) {
			$this->data['User']['password'] =
			AuthComponent::password($this->data['User']['password']);	
		}
		return true;
	}

/*	public $belonsTo = array('Group');
	public $actsAs = array('Acl' => array('type' => 'requester'));

	public function parentNode() {
		if (!$this->id && empty($this->data)) {
			return null;
		}

		if (isset($this->data['User']['group_id'])) {
			$groupId = $this->data['User']['group_id'];
		} else {
			$groupId = $this->field('group_id');
		}
		if (!$groupId) {
			return null;
		} else {
			return array('Group' => array('id' => $groupId));
		}
	}

	public function bindNode($user) {
 	   return array('model' => 'Group', 'foreign_key' => $user['User']['group_id']);
	}

*/
}