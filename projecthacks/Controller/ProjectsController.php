<?php

class ProjectsController extends AppController {
	 public $uses = array('Project', 'Skill', 'Tool', 'Framework', 'Db', 'Contract', 'Position', 'Service', 'Prefecture', 'City', 'Liquidation' );
	 public $layout = '';
	// public $scaffold;

	 public function index() {

		if ($this->request->is('get')) {

			$join = $this->Project->joins();
			$freeword = array();
			$conditions = array();

			if (isset($this->request->query['skill']) && !empty($this->request->query['skill'])) {

				$conditions = array_merge($conditions, array(
					'ProjectsSkill.skill_id' => $this->request->query['skill']
				));

			}

			if (isset($this->request->query['position']) && !empty($this->request->query['position'])) {
				
				$conditions = array_merge($conditions, array(
					'Project.position_id' => $this->request->query['position']
				));
			}

			if (isset($this->request->query['price']) && !empty($this->request->query['price'])) {

				$conditions = array_merge($conditions,array(
					'Project.min_price >' => $this->request->query['price']
				));
			}

			if (isset($this->request->query['freeword'])&& !empty($this->request->query['freeword'])) {

				$keyword = str_replace("　", " ", $this->request->query['freeword']);
				$key_array = explode( " " , $keyword );

				for( $i=0; $i < count($key_array); $i++ ) {
					$title = "Project.title like '%$key_array[$i]%'";
					$content = "Project.content like '%$key_array[$i]%'";
					$conditions = array_merge($conditions, array(array("OR" => array($title,$content))));
				}
				
			}

			$conditions = array_merge($conditions, array('ContractsProject.project_id = Project.id'));

			$this->paginate = array(
				'limit' => 10,
				'paramType' => 'querystring',
				'joins' => $join,
				'conditions' => array($conditions),
				'fields' => array(
					'Project.id', 'Project.title', 'Project.station', 'Project.min_price','Project.max_price',
					'Project.meeting', 'Project.must_skill', 'Project.content','Liquidation.name', 'Contract.name',
				)
			);

		} else {

			$this->paginate = array(
				'limit' => 10,
				'paramType' => 'querystring',
				'fields' => array(
					'Project.id', 'Project.title', 'Project.station', 'Project.min_price','Project.max_price',
					'Project.meeting', 'Project.must_skill', 'Project.content','Liquidation.name', 'Contract.name',
				)
			);
		}

		$price = array();
		for( $i = 3; $i < 11; $i++ ) {
			$price[$i.'0'] = $i.'0';
		}
		$this->set('price', $price);
		$this->set('count', $this->Project->find('count'));
		$this->set('skill', $this->Project->Skill->find('list'));
		$this->set('position', $this->Position->find('list'));
		$this->set('project', $this->paginate());

	}

	public function detail() {

	 	$params = $this->params['id'];
	 	$project = $this->Project->find('first', array(
	 		'conditions' => array('Project.id' => $params),
	 		'recursive' => 0,
	 		'limit' => 1
	 	));

	 	if ($project == null) {

	 		// throw new NotFoundException();
	 		return $this->redirect(array('controller' => 'Homes', 'action' => 'index'));
	 	}

		$price = array();
		for( $i = 3; $i < 11; $i++ ) {
			$price[$i.'0'] = $i.'0';
		}
		$this->set('price', $price);
		$this->set('count', $this->Project->find('count'));
		$this->set('skill', $this->Project->Skill->find('list'));
		$this->set('position', $this->Position->find('list'));
	 	$this->set('count', $this->Project->find('count'));
	 	$this->set('project', $project);
	}
}