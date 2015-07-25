<?php

class ProjectsController extends AppController {
	 public $uses = array('Project', 'Skill', 'Contract', 'Position');
	 public $components = array('RequestHandler', 'Session');
	// public $layout = '';
	// public $scaffold;

	 public function index() {

		$keep_id = null;
		$keep_count = 0;
		$freeword = array();
		$conditions = array();

		$fields = array(
			'Project.id', 'Project.title', 'Project.station', 'Project.min_price','Project.max_price',
			'Project.meeting', 'Project.must_skill', 'Project.content','Liquidation.name','Position.name',
		);

		if ($this->request->is('get')) {

			// 検索条件にSkillがセットされている場合
			if (isset($this->request->query['Skill']) && !empty($this->request->query['Skill'])) {
				$conditions = array_merge($conditions, array(
					'Project.primary_skill_id' => $this->request->query['Skill']
				));
			}

			// 検索条件にPositionがセットされている場合
			if (isset($this->request->query['Position']) && !empty($this->request->query['Position'])) {
				$conditions = array_merge($conditions, array(
					'Project.position_id' => $this->request->query['Position']
				));
			}

			// 検索条件にPriceがセットされている場合
			if (isset($this->request->query['price']) && !empty($this->request->query['price'])) {
				$conditions = array_merge($conditions,array(
					'Project.min_price >' => $this->request->query['price']
				));
			}

			// 検索条件にfreewordがセットされている場合
			if (isset($this->request->query['freeword'])&& !empty($this->request->query['freeword'])) {

				$replace = array("　", ",", ".", "/", "¥", "|", "<", ">", "?", "\\", "、","(", ")", "$", "#", "%", "&");
				$keyword = str_replace($replace, " ", $this->request->query['freeword']);
				$key_array = explode( " " , $keyword );

				for( $i=0; $i < count($key_array); $i++ ) {
					$title = "Project.title like '%$key_array[$i]%'";
					$content = "Project.content like '%$key_array[$i]%'";
					$conditions = array_merge($conditions, array(array("OR" => array($title,$content))));
				}
			}
			
			$id_list = $this->Project->find('list', array(
				'fields' => 'Project.id',
				'conditions' => $conditions
			));

			$this->paginate = array(
				'limit' => 10,
				'paramType' => 'querystring',
				'recursive' => 1,
				'conditions' => array('Project.id' => $id_list),
				'fields' => $fields,
			);

		} else {

			$this->paginate = array(
				'limit' => 10,
				'paramType' => 'querystring',
				'recursive' => 1,
				'fields' => $fields,
			);
		}

	 	if ( $this->Session->check('keep_id') && $this->Session->check('keep_count') ) {
	 		$keep_id = $this->Session->read('keep_id');
	 		$keep_count = $this->Session->read('keep_count');
	 	}

		$sub_conditions = array('Project.primary_skill_id' => array(1,2,3,6,7,8));
		$sub_fields = array('Project.id', 'Project.title', 'Project.station', 'Project.min_price', 'Project.max_price', 'Position.name');
	 	$sub_id_list = $this->Project->find('list', array(
	 		'fields' => 'Project.id',
	 		'order' => 'rand()',
	 		'conditions' => $sub_conditions,
	 		'limit' => 4
	 	));

		$this->set('price', $this->Project->price());
		$this->set('skills', $this->Project->PrimarySkill->find('list'));
		$this->set('positions', $this->Project->Position->find('list'));
 		$this->set('keep_id', $keep_id);
 		$this->set('keep_count', $keep_count);
		$this->set('project', $this->paginate());
		$this->set('sub_project', $this->Project->find('all', array('recursive' => 1,'fields' => $sub_fields, 'conditions' => $sub_conditions, 'limit' => 4)));

	}

	public function detail() {

		$keep_id = array();
		$keep_count = 0;

		// idを取得
	 	$params = $this->params['id'];
	 	$project = $this->Project->find('first', array(
	 		'conditions' => array('Project.id' => $params),
	 		'recursive' => 1,
	 		'limit' => 1
	 	));

	 	// もし案件が見つからなければトップページへ飛ばす
	 	if ($project == null) {
	 		// throw new NotFoundException();
	 		return $this->redirect(array('action' => 'index'));
	 	}
	 	
	 	// セッションチェック
	 	if ( $this->Session->check('keep_id') && $this->Session->check('keep_count') ) {
	 		$keep_id = $this->Session->read('keep_id');
	 		$keep_count = $this->Session->read('keep_count');
	 	}

	 	if ( in_array($params, $keep_id) ) {
	 		$keep_id = true;
	 	} else {
	 		$keep_id = false;
	 	}

	 	$sub_conditions = array(
	 		'OR' => array(
		 		'Project.position_id' => $project['Project']['position_id'],
		 		'Project.primary_skill_id' => $project['Project']['primary_skill_id']
		 	)
	 	);

	 	$sub_id_list = $this->Project->find('list', array(
	 		'fields' => 'Project.id',
	 		'order' => 'rand()',
	 		'conditions' => $sub_conditions,
	 		'limit' => 4
	 	));

	 	$sub_fields = array('Project.id', 'Project.title', 'Project.station', 'Project.min_price', 'Project.max_price', 'Position.name');

		$this->set('keep_count', $keep_count);
		$this->set('keep_id', $keep_id);
	 	$this->set('project', array($project));
	 	$this->set('sub_project', $this->Project->find('all', array(
	 		'recursive' => 1,
	 		'fields' => $sub_fields,
	 		'conditions' => array('Project.id' => $sub_id_list),
	 	)));
	}

	public function home() {

		$keep_id = null;
		$keep_count = 0;
		$fields = array('Project.id', 'Project.title', 'Project.station', 'Project.min_price', 'Project.max_price', 'Position.name');
		$sub_conditions = array('Project.primary_skill_id' => array(1,2,3,6,7,8));

	 	if ( $this->Session->check('keep_id') && $this->Session->check('keep_count') ) {
	 		$keep_id = $this->Session->read('keep_id');
	 		$keep_count = $this->Session->read('keep_count');
	 	}

		$this->set('price', $this->Project->price());
		$this->set('skills', $this->Project->PrimarySkill->find('list'));
		$this->set('positions', $this->Project->Position->find('list'));		
 		$this->set('keep_count', $keep_count);
		$this->set('project', $this->paginate());
		$this->set('pickup_project', $this->Project->find('all', array('recursive' => 1,'fields' => $fields, 'limit' => 6)));
		$this->set('sub_project', $this->Project->find('all', array('recursive' => 1,'fields' => $fields, 'conditions' => $sub_conditions, 'limit' => 4)));
	}

}