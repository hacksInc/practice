<?php 

class ContactsController extends AppController {

	public $uses = array('Contact');
	public $components = array('RequestHandler', 'Session');
	public $layout = "contact";

	public function person(){

		if (isset($this->request->data['submit']) && $this->request->is(array('post' , 'put'))) {

			$this->Contact->set($this->request->data);

			if ( $this->Contact->validates()) {

				$data = $this->request->data['Contact'];
				$this->Session->write('session', $data);
				$this->set('contact', $this->request->data['Contact']);
				$this->disableCache();
				$this->render('confirm_person');

			} else {

				$this->Session->setFlash($this->Contact->validationErrors);

			}
		}
	}

	public function company(){

		if (isset($this->request->data['submit']) && $this->request->is(array('post' , 'put'))) {

			$this->Contact->set($this->request->data);

			if ( $this->Contact->validates()) {

				$data = $this->request->data['Contact'];
				$this->Session->write('session', $data);
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
			$this->Session->delete('session');
			$this->disableCache();
		
		} else {
		
			$this->redirect(array('action' => 'person'));
		}
	}
}