<?php
App::uses('Folder', 'Utility');
class MembersController extends AppController {

	public $uses = array('Member', 'File', 'Project');
	public $components = array('RequestHandler', 'Session');
	public $layout = "contact";

	public function index(){

		$keep_id = null;

		if ( isset($this->request->data['keep']) && !empty($this->request->data['Member']['id']) ) {
			$keep_id = $this->request->data['Member']['id'];
		}
		
		$fields = array(
			'Project.id', 'Project.title', 'Project.station', 'Project.min_price','Project.max_price', 'Position.name'
		);

		$keep_project = $this->Project->find('all', array(
			'fields' => $fields,
			'recursive' => 1,
			'conditions' => array('Project.id' => $keep_id),
		));

		$this->set('year', $this->Member->year());
		$this->set('month', $this->Member->month());
		$this->set('day', $this->Member->day());
		$this->set('project', $keep_project);

		if (isset($this->request->data['submit']) && $this->request->is(array('post' , 'put'))) {

			$this->Member->set($this->request->data);

			if ( $this->Member->validates()) {
				$data = $this->request->data['Member'];

				if (!empty($data['File']['tmp_name'])) {
					$dir = new Folder(WWW_ROOT.'files', true);
					$filename = md5(mt_rand());
					if ( move_uploaded_file($data['File']['tmp_name'], 'files/'.$filename)) {
						$file = array(
							'filename' => $filename,
							'type' => $data['File']['type'],
							'contents' => 'files/'.$filename,
						);
						$data['File'] = $file;
					}
				}
				$data['birth'] =  date('Y-m-d', $data['year'].$data['month'].$data['day']);
				$this->Session->write('session', $data);
				$this->set('member', $this->request->data['Member']);
				$this->disableCache();
				$this->render('confirm');

			} else {

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
				$dir = new Folder(WWW_ROOT.'files');
				$dir->delete();
				$this->Session->delete('session');
				$this->disableCache();
			} 
		} else {
			$this->redirect(array('action' => 'index'));
		}
	}
}
