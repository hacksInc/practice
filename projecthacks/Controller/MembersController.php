<?php
App::uses('Folder', 'Utility');
class MembersController extends AppController {

	public $uses = array('Member', 'File');
	public $layout = "";
	public $components = array('Session');

	public function index(){

		$this->set('year', $this->Member->year());
		$this->set('month', $this->Member->month());
		$this->set('day', $this->Member->day());
		$this->Session->write('key', 1);
	}

	public function confirm() {

		if ($this->request->is(array('post' , 'put')) && !empty($this->request->data && $this->Session->read('key') == 1)) {

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

		} else {
			$this->redirect(array('action' => 'index'));
		}
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
				$this->Session->setFlash('応募が完了しました');
			} else {
				$this->Session->setFlash('エラーが発生しました。');

			}
		} else {
			$this->redirect(array('action' => 'index'));
		}
	}
}
