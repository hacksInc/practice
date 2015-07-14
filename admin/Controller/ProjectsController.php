<?php

class ProjectsController extends AppController {

	public $uses = array('Project', 'Skill', 'Tool', 'Framework', 'Db', 'Contract', 'Position', 'Service', 'Prefecture', 'City', 'Liquidation' );
	public $helpers = array('Html', 'Form');

	public function index() {
		$this->set('title_for_layout', '案件一覧');
		$this->paginate = array(
			'Project' => array(
				'fields' => array('Project.id', 'Project.title', 'Project.min_price', 'Project.max_price', 'Project.station', 'Project.modified'),
				'limit' => 18,
				'order' => array('Project.id' => 'desc')
			)
		);
		$this->set('projects', $this->paginate());
	}

	public function add() {
		$this->set('title_for_layout', '新規登録');

		if ($this->request->is(array('post', 'put'))) {
			if ($this->Project->saveAssociated($this->request->data, array('deep' => true))) {
				$this->Session->setFlash('Success!');
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash('failed!');
			}
		}

		$this->set('skills', $this->Project->Skill->find('list'));
		$this->set('tools', $this->Project->Tool->find('list'));
		$this->set('frameworks', $this->Project->Framework->find('list'));
		$this->set('dbs', $this->Project->Db->find('list'));
		$this->set('contracts', $this->Project->Contract->find('list'));

		$this->set('service', $this->Service->find('list'));
		$this->set('position', $this->Position->find('list'));
		$this->set('prefecture', $this->Prefecture->find('list'));
		$this->set('city', $this->City->find('list'));
		$this->set('liquidation', $this->Liquidation->find('list'));


	}

	public function edit($id = null) {
		$this->set('title_for_layout', '編集画面');

		if (!$id) {
        	throw new NotFoundException(__('Invalid post'));
    	}

    	$post = $this->Project->find('first',array(
    		'conditions' => array('Project.id' => $id),
    		'recursive' => 1
    	));

    	$this->set('id', $post);
		$this->set('skills', $this->Project->Skill->find('list'));
		$this->set('tools', $this->Project->Tool->find('list'));
		$this->set('frameworks', $this->Project->Framework->find('list'));
		$this->set('dbs', $this->Project->Db->find('list'));
		$this->set('contracts', $this->Project->Contract->find('list'));

		$this->set('service', $this->Service->find('list'));
		$this->set('position', $this->Position->find('list'));
		$this->set('prefecture', $this->Prefecture->find('list'));
		$this->set('city', $this->City->find('list'));
		$this->set('liquidation', $this->Liquidation->find('list'));

		if (!$post) {
        	throw new NotFoundException(__('Invalid post'));
	    }

	    if ($this->request->is(array('post', 'put'))) {
	    	$this->Project->id = $id;
			if ($this->Project->save($this->request->data)) {
				$this->Session->setFlash('保存しました');
				return $this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash('failed!');
			} 
		}

 	    if (!$this->request->data) {
        	$this->request->data = $post;
		}
    }

	public function delete($id) {
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		if ($this->Project->delete($id)) {
			$this->Session->setFlash('Deleted!');
			$this->redirect(array('action'=>'index'));
		}
	}
}



		/*
		if ($this->request->is('ajax')) {
			if ($this->Project->delete($id)) {
				$this->autoRender = false;
				$this->autoLayout = false;
				$response = array('id' => $id);
				$this->header('Content-Type: application/json');
				echo json_encode($response);
				exit();
			}
		}
		$this->redirect(array('action'=>'kanri'));
		*/

