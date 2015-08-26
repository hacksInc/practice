<?php
App::uses('Folder', 'Utility');

class MembersController extends AppController {

	public $uses = array('Member');

	public function index() {

		$this->paginate = array(
			'Member' => array(
				'fields' => array(),
				'recursive' => 1,
				'limit' => 18,
			)
		);

		$this->set('css', 'member');
		$this->set('members', $this->paginate('Member'));

	}

	public function edit($id=null) {

		if (!$id) {
        	throw new NotFoundException(__('Invalid post'));
    	}

    	$post = $this->Member->find('first',array(
    		'conditions' => array('Member.id' => $id),
    		'recursive' => 1
    	));

		if (!$post) {
        	throw new NotFoundException(__('Invalid post'));
	    }

	    if ($this->request->is(array('post', 'put'))) {

	    	$data = $this->request->data;
	    	$data['Member']['id'] = $post['Member']['id'];

			if (!empty($data['Member']['File']['tmp_name'])) {
				$filename = md5(mt_rand());
				$file = array(
					'id' => $post['Member']['file_id'],
					'filename' => $filename,
					'type' => $data['Member']['File']['type'],
					'contents' => file_get_contents($data['Member']['File']['tmp_name'])
				);
				$data['Member']['File'] = $file;
			}
			
			if ($this->Member->saveAssociated($data, array('deep' => true))) {
				$this->Session->setFlash('保存しました');
				return $this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash('failed!');
			}
		}

 	    if (!$this->request->data) {
        	$this->request->data = $post;
		}

		if(empty($post['File']['filename'])) {
			$this->set('empty', 'スキルシートはありません。');
		}

		$this->set('data', $post);
		$this->set('css', 'member');
	}

	public function download($id=null){

		if (!$id) {
        	throw new NotFoundException(__('Invalid post'));
    	}

		$this->autoRender = false;
		$exe = null;

    	$post = $this->Member->find('first',array(
    		'conditions' => array('Member.id' => $id),
    		'recursive' => 1,
    		'fields' => array('File.type', 'File.filename', 'File.contents')
    	));


		$type = $post['File']['type'];
		$docx = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
		$xlsx = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
		$pptx = "application/vnd.openxmlformats-officedocument.presentationml.presentation";

		if( $type == $docx ) {
			$exe = '.docx';
		} elseif ( $type == $xlsx ) {
			$exe = '.xlsx';
		} elseif ( $type == $pptx ) {
			$exe = '.pptx';
		}


		$this->response->type($post['File']['type']);
		$this->response->download($post['File']['filename'].$exe);
		$this->response->body($post['File']['contents']);


		
	}
}