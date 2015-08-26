<?php
App::uses('Folder', 'Utility');
App::uses('CakeEmail', 'Network/Email');

class MembersController extends AppController {

	public $uses = array('Member', 'File', 'Project');
	public $layout = "contact";
	 
	public function index(){

		$entry_id = null;

		if ( isset($this->request->data['keep']) && !empty($this->request->data['Member']['entry_id']) ) {
			$entry_id = $this->request->data['Member']['entry_id'];
		}
		
		$entry_project = $this->Project->entry($entry_id);

		$this->set('title', '新規無料登録 | IT/webフリーランスの案件/求人情報Connect(コネクト)');
		$this->set('keywords', '新規無料登録,フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事');
		$this->set('description', 'ITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイトConnect(コネクト)の新規無料登録ページです。');
		$this->set('css', 'member');
		$this->set('js', 'member');

		$this->set('year', $this->Member->year());
		$this->set('month', $this->Member->month());
		$this->set('day', $this->Member->day());
		$this->set('project', $entry_project);

		if (isset($this->request->data['submit']) && $this->request->is(array('post' , 'put'))) {

			$this->Member->set($this->request->data);

			if ( $this->Member->validates()) {

				$data = $this->Member->input($this->request->data['Member']);

				$this->set('title', '入力内容確認 | IT/webフリーランスの案件/求人情報Connect(コネクト)');
				$this->set('keywords', '入力内容確認,フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事');
				$this->set('description', 'ITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイトConnect(コネクト)の入力内容確認ページです。');
				$this->set('css', 'member');
				$this->set('js', 'member');

				$this->Session->write('session', $data);
				$this->set('member', $this->request->data['Member']);
				$this->disableCache();
				$this->render('confirm');

			} else {

				$entry_id = null;

				if( isset($this->request->data['Member']['Project']) ) {
					$entry_id = $this->request->data['Member']['Project'];
				}

				$entry_project = $this->Project->entry($entry_id);

				$this->set('project', $entry_project);
				$this->Session->setFlash($this->Member->validationErrors);

			}
		}
	}

	public function confirm() {
		$this->redirect(array('action' => 'index'));
	}

	public function complete() {

		if($this->request->is(array('post' , 'put'))) {

			$this->Session->delete('key');
			$data = $this->Session->read('session');

			if( !empty($data['File']['contents']) ) {
				$data['File']['contents'] = file_get_contents('files/'.$data['File']['filename']);
			}

			if ($this->Member->saveAssociated($data, array('deep' => true))) {

				$this_id = $this->Member->getLastInsertID();
				$this->_email($data, $this_id);
				$dir = new Folder(WWW_ROOT.'files');
				$dir->delete();
				$this->Session->delete('session');
				$this->disableCache();
				$this->set('title', '新規無料登録完了 | Connect(コネクト) IT/webフリーランスの案件/求人情報');
				$this->set('keywords', '新規無料登録完了,フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事');
				$this->set('description', 'ITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイトConnect(コネクト)の新規無料登録完了ページです。');
				$this->set('css', 'member');
				$this->set('js', 'complete');
			}
		} else {
			$this->redirect(array('action' => 'index'));
		}
	}

	public function _email($data, $this_id) {

/*				$email = new CakeEmail('connect');
				$email->config(array(
		            'template' => 'member',
		            'to' => $data['email'],
		            'subject' => '【自動送信メール】エントリーありがとうございます。',
		        	'viewVars' => array(
		        		'message' => "エントリーを受け付けました。\nこのメールは自動で送信されております。"
		            ),
		        ));
		        foreach ($data as $key => $value) {
			        $email->viewVars(array($key => $value));
			    }
				$email->send();
*/
				$email = new CakeEmail('connect');
				$email->config(array(
		            'template' => 'member',
		            'to' => 'connect@hacks.co.jp',
		            'subject' => 'エントリーがありました。',
		            'viewVars' => array(
		            	'message' => "エントリーがありました。id：".$this_id
		            ),
		        ));
		        foreach ($data as $key => $value) {
			        $email->viewVars(array($key => $value));
			    }
				$email->send();
	}

}
