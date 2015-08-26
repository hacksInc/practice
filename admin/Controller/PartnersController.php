<?php

class PartnersController extends AppController {

	public $uses = array('Partner');

	public function index() {

		$conditions = array();

    	if ($this->request->is('post') && isset($this->request->data['Partner']['search']) ) {

    		$data = $this->request->data['Partner']['search'];
    		$replace = array("　", ",", ".", "/", "¥", "|", "<", ">", "?", "\\", "、","(", ")", "$", "#", "%", "&");
			$keyword = str_replace($replace, " ", $data);
			$key_array = explode( " " , $keyword );

			for( $i=0; $i < count($key_array); $i++ ) {
				$company = "Partner.company like '%$key_array[$i]%'";
				$company_kana = "Partner.company_kana like '%$key_array[$i]%'";
				$ses_email = "Partner.ses_email like '%$key_array[$i]%'";				
				$conditions = array_merge($conditions, array(array("OR" => array($company,$company_kana,$ses_email))));
			}
		}

		$this->paginate = array(
			'fields' => array('id', 'company', 'modified'),
			'order' => 'modified DESC',
			'limit' => 18,
			'conditions' => $conditions
		);

		$this->set('partner', $this->paginate());
		$this->set('css', 'partner');

	}

	public function add() {

		if ($this->request->is(array('post', 'put'))) {
			if ($this->Partner->save($this->request->data)) {
				$this->Session->setFlash('Success!');
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash('failed!');
			}
		}

		$this->set('css', 'partner');

	}

	public function edit($id=null) {


		if (!$id) {
        	throw new NotFoundException(__('Invalid post'));
    	}

    	$post = $this->Partner->find('first',array(
    		'conditions' => array('Partner.id' => $id),
    		'recursive' => 1
    	));

		if (!$post) {
        	throw new NotFoundException(__('Invalid post'));
	    }

	    if ($this->request->is(array('post', 'put'))) {
	    	$this->Partner->id = $id;
			if ($this->Partner->save($this->request->data)) {
				$this->Session->setFlash('保存しました');
				return $this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash('failed!');
			} 
		}

 	    if (!$this->request->data) {
        	$this->request->data = $post;
		}

		$this->set('css', 'partner');

	}

	public function delete($id) {
		
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}

		if ($this->Partner->delete($id)) {
			$this->Session->setFlash('Deleted!');
			$this->redirect(array('action'=>'index'));
		}
	}
	
    function download() {

    	$this->layout = false;

    	$post = $this->Partner->find('list',array(
    		'fields' => array('ses_email')
    	));

    	$this->set('post', $post);

	}
}