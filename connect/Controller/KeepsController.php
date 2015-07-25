<?php

class KeepsController extends AppController {

	public $uses = array('Project');
	public $components = array( 'RequestHandler', 'Session');
	//public $layout = '';

	public function index() {

		$keep_id = null;
		$keep_count = 0;
		$conditions = false;

		// keepしている案件のセッションチェック
		if ( $this->Session->check('keep_id') && $this->Session->check('keep_count') ) {

			$keep_id = $this->Session->read('keep_id');
			$keep_count = $this->Session->read('keep_count');
			$conditions = array('Project.id' => $keep_id);

		}

		// keepしている案件のidリスト
		$id_list = $this->Project->find('list', array(
			'fields' => 'Project.id',
			'conditions' => $conditions
		));

		// 取得する案件のフィールド
		$fields = array(
			'Project.id', 'Project.title', 'Project.station', 'Project.min_price','Project.max_price',
			'Project.meeting', 'Project.must_skill', 'Project.content','Liquidation.name','Position.name',
		);

		// ページネイト
		$this->paginate = array(
			'limit' => 10,
			'paramType' => 'querystring',
			'recursive' => 1,
			'conditions' => array('Project.id' => $id_list),
			'fields' => $fields,
		);

		// サブ案件のコンディション
		$sub_conditions = array('Project.primary_skill_id' => array(1,2,3,6,7,8));

		// サブ案件のフィールド
		$sub_fields = array('Project.id', 'Project.title', 'Project.station', 'Project.min_price', 'Project.max_price', 'Position.name');

		// サブ案件のidリストをランダムで取得
	 	$sub_id_list = $this->Project->find('list', array(
	 		'fields' => 'Project.id',
	 		'order' => 'rand()',
	 		'conditions' => $sub_conditions,
	 		'limit' => 4
	 	));

	 	$this->set('price', $this->Project->price());
		$this->Set('keep_count', $keep_count);
		$this->Set('keep_id', $keep_id);
		$this->set('skills', $this->Project->PrimarySkill->find('list'));
		$this->set('positions', $this->Project->Position->find('list'));
		$this->set('project', $this->paginate());
	 	$this->set('sub_project', $this->Project->find('all', array(
	 		'recursive' => 1,
	 		'fields' => $sub_fields,
	 		'conditions' => array('Project.id' => $sub_id_list),
	 	)));
    }

    public function add() {

        $this->autoRender = FALSE;

	    if(!$this->request->is('ajax')) {
	      
	 		// throw new NotFoundException();
	 		return $this->redirect(array('action' => 'index'));

	    } else {

	    	$result = json_decode($this->data['name']);
	    	$keep_id = array($result);

	    	if($this->Session->check('keep_id')) {

	    		$session = $this->Session->read('keep_id');

	    		if (in_array($result, $session)) {

	    			$keep_id = $session;

	    		} else {

	    			$keep_id = array_merge($session, $keep_id);
	    			
	    		}
	    	}

	    	$keep_count = count($keep_id);

	    	$this->Session->write('keep_id', $keep_id);
	    	$this->Session->write('keep_count', $keep_count);
	    	//$this->Session->destroy('keep');

	    	return json_encode(compact('keep_id', 'keep_count'));
	    }
    }

    public function delete() {

        $this->autoRender = FALSE;

	    if(!$this->request->is('ajax')) {

	 		// throw new NotFoundException();
	 		return $this->redirect(array('action' => 'index'));

	    } else {

	    	$result = json_decode($this->data['name']);
	    	$keep_id = array($result);

	    	if($this->Session->check('keep_id')) {

	    		$session = $this->Session->read('keep_id');
	    		$key = array_search($result, $session, true);

	    		if ( $key !== false ) {

	    			unset($session[$key]);
	    			$keep_id = array_values($session);

	    		} else {

	    			$keep_id = $session;
	    		}
	    	}

	    	$keep_count = count($keep_id);

	    	$this->Session->write('keep_id', $keep_id);
	    	$this->Session->write('keep_count', $keep_count);
	    	//$this->Session->destroy('keep');

	    	return json_encode(compact('keep_id', 'keep_count'));
	    }
    }

    public function all_delete() {

        $this->autoRender = FALSE;

	    if(!$this->request->is('ajax')) {

	 		// throw new NotFoundException();
	 		return $this->redirect(array('action' => 'index'));

	    } else {

	    	$keep_id = null;
	    	$keep_count = 0;

	    	$this->Session->write('keep_id', $keep_id);
	    	$this->Session->write('keep_count', $keep_count);

	    	return json_encode(compact('keep_id', 'keep_count'));
	    }

    }

    public function check() {

        $this->autoRender = FALSE;

	    if(!$this->request->is('ajax')) {

	 		// throw new NotFoundException();
	 		return $this->redirect(array('action' => 'index'));

	    } else {

	    	$keep_id = null;
	    	$keep_count = 0;

	    	if($this->Session->check('keep_id') && $this->Session->check('keep_count')) {

	    		$keep_id = $this->Session->read('keep_id');
	    		$keep_count = $this->Session->read('keep_count');

	    	}

	    	return json_encode(compact('keep_id', 'keep_count'));
	    }
    }

}