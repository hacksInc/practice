<?php 
App::uses('CakeEmail', 'Network/Email');

class ContactsController extends AppController {

	public $uses = array('Contact');
	public $layout = "contact";

	public function person(){

		$this->set('title', '個人の方のお問い合わせ | Connect(コネクト) IT/webフリーランスの案件/求人情報');
		$this->set('keywords', '個人,お問い合わせ,フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事');
		$this->set('description', 'ITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイトConnect(コネクト)の個人の方のお問い合わせページです。');
		$this->set('css', 'contact');
		$this->set('js', 'member');

		if (isset($this->request->data['submit']) && $this->request->is(array('post' , 'put'))) {

			$this->Contact->set($this->request->data);

			if ( $this->Contact->validates()) {

				$data = $this->request->data['Contact'];

				$this->set('title', '入力内容確認(お問い合わせ) | Connect(コネクト) IT/webフリーランスの案件/求人情報');
				$this->set('keywords', 'お問い合わせ,入力内容確認,フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事');
				$this->set('description', 'ITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイトConnect(コネクト)のお問い合わせ入力内容確認ページです。');
				$this->set('css', 'contact');
				$this->set('js', 'member');
				$this->Session->write('session', $data);
				$this->Session->write('contact_check', 'person');
				$this->set('contact', $this->request->data['Contact']);
				$this->disableCache();
				$this->render('confirm_person');

			} else {

				$this->Session->setFlash($this->Contact->validationErrors);

			}
		}
	}

	public function company(){

		$this->set('title', '法人様のお問い合わせ | Connect(コネクト) IT/webフリーランスの案件/求人情報');
		$this->set('keywords', '法人,お問い合わせ,フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事');
		$this->set('description', 'ITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイトConnect(コネクト)の法人様のお問い合わせページです。');
		$this->set('css', 'contact');
		$this->set('js', 'member');

		if (isset($this->request->data['submit']) && $this->request->is(array('post' , 'put'))) {

			$this->Contact->set($this->request->data);

			if ( $this->Contact->validates()) {

				$data = $this->request->data['Contact'];

				$this->set('title', '入力内容確認(法人様お問い合わせ) | Connect(コネクト) IT/webフリーランスの案件/求人情報');
				$this->set('keywords', '法人様お問い合わせ,入力内容確認,フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事');
				$this->set('description', 'ITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイトConnect(コネクト)の法人様お問い合わせ入力内容確認ページです。');
				$this->set('css', 'contact');
				$this->set('js', 'member');
				$this->Session->write('session', $data);
				$this->Session->write('contact_check', 'company');
				$this->set('contact', $this->request->data['Contact']);
				$this->disableCache();
				$this->render('confirm_company');


			} else {

				$this->Session->setFlash($this->Contact->validationErrors);

			}
		}
	}

	public function confirm() {
		$this->redirect(array('action' => 'person'));
	}

	public function complete() {

		if($this->request->is(array('post' , 'put'))) {

			$data = $this->Session->read('session');
			$this->set('data', $data);

			$this->set('title', '新規無料登録完了 | Connect(コネクト) IT/webフリーランスの案件/求人情報');
			$this->set('keywords', '新規無料登録完了,フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事');
			$this->set('description', 'ITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイトConnect(コネクト)の新規無料登録完了ページです。');
			$this->set('css', 'member');
			$this->set('js', 'complete');
			$this->_email($data);

			$this->Session->delete('session');
			$this->Session->delete('contact_check');
			$this->disableCache();
		
		} else {
		
			$this->redirect(array('action' => 'person'));
		}
	}
	public function _email($data) {

/*			// 問い合わせ者に自動送信
			$email = new CakeEmail('connect');
			$email->config(array(
		    	'template' => $this->Session->read('contact_check'),
		        'to' => $data['email'],
		        'subject' => '【自動送信メール】お問い合わせを受け付けました。',
		        'viewVars' => array(
		        	'message' => "お問い合わせを受け付けました。\nこのメールは自動で送信されております。"
		        ),
			));
	        foreach ($data as $key => $value) {
		        $email->viewVars(array($key => $value));
		    }
			$email->send();
*/
			// お問い合わせ通知メール
			$email = new CakeEmail('connect');
			$email->config(array(
	            'template' => $this->Session->read('contact_check'),
		    	'to' => 'connect@hacks.co.jp',
		        'subject' => 'お問い合わせがありました。',
		        'viewVars' => array(
		        	'message' => 'お問い合わせがありました。',
		        ),
		    ));
		    foreach ($data as $key => $value) {
				$email->viewVars(array($key => $value));
			}
			$email->send();

	}
}