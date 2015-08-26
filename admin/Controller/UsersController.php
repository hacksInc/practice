<?php

class UsersController extends AppController {
	public $helpers = array('Html', 'Form');
	public function index() {

	}

/*    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add');
    }
*/
	public function users() {
		$this->paginate = array(
			'limit' => 20,
			'sort' => 'modified',
			'direction' => 'desc'
			);
		$this->set('users', $this->paginate());
	}

	public function login() {
		$this->autoLayout = false;
		$this->Session->destroy();
		if ($this->request->is('post')) {
			if($this->Auth->login()) {
				$this->redirect($this->Auth->redirect(array('controller' => 'projects', 'action' => 'index')));
			} else {
				$this->Session->setFlash('ユーザ名かパスワードが違います', 'default', array(), 'auth');

			}
		}
	}

	public function logout() {
		$this->Session->setFlash('ログアウトしました');
		$this->redirect($this->Auth->logout(array('controller' => 'users', 'action' => 'login')));
	}

	public function add() {
		if ($this->request->is('post')) {
            	$this->User->create();
            	if ($this->User->save($this->request->data)) {
                	$this->Session->setFlash(__('登録しました'));
                	$this->redirect(array('action' => 'login'));
            } else {
                $this->Session->setFlash(__('登録できませんでした'));
            }
		}
	}

	public function delete($id) {
		if ($this->request->is('get')) {
			throw new MethodNotAllowedException();
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash('Deleted!');
			$this->redirect(array('action'=>'index'));
		}
	}
}
