<?php

class KeepsController extends AppController {

	public $uses = array('Project');

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
			'Project.id', 'Project.title', 'Project.station','Project.meeting', 'Project.must_skill', 'Project.content',
			'Liquidation.name','Position.name', 'MinPrice.name','MaxPrice.name',
		);

		// ページネイト
		$this->paginate = array(
			'limit' => 10,
			'paramType' => 'querystring',
			'recursive' => 1,
			'conditions' => array('Project.id' => $id_list),
			'fields' => $fields,
		);

		$this->set('h1', '気になる案件/求人リスト | IT/webフリーランスの案件/求人情報');
		$this->set('title', '気になる案件/求人リスト |  IT/webフリーランスの案件/求人情報Connect(コネクト)');
		$this->set('keywords', '気になる,フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事');
		$this->set('description', 'ITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイトConnect(コネクト)の気になる案件/求人リスト。あなたが気になった案件/求人を一覧で見る事が出来ます。');
		$this->set('ogtype', 'article');
		$this->set('ogurl', 'https://connect-job.com/keeps');
		$this->set('css', 'keep');
		$this->set('js', 'keep');

		$this->Set('keep_count', $keep_count);
		$this->Set('keep_id', $keep_id);
		$this->set('price', $this->Project->price_format());
		$this->set('skills', $this->Project->skills());
		$this->set('positions', $this->Project->positions());
		$this->set('project', $this->paginate());
		$this->set('sub_project', $this->Project->sidebar());
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