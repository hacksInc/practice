<?php

class HomesController extends AppController {

	public $uses = array('Project', 'Skill', 'Tool', 'Framework', 'Db', 'Contract', 'Position', 'Service', 'Prefecture', 'City', 'Liquidation' );
	//public $helpers = array('');
	public $layout = '';

	public function index() {

		$join = array(
			array(
				'table' => 'projects_skills',
				'alias' => 'ProjectsSkill',
				'type' => 'inner',
				'conditions' => array(
					'Project.id = ProjectsSkill.project_id')
			),
			array(
				'table' => 'skills',
				'alias' => 'Skill',
				'type' => 'inner',
				'conditions' => array(
					'ProjectsSkill.skill_id = Skill.id')
			),
		);

		$condition = array(
			'ProjectsSkill.skill_id' => array(1,2,3,4),
		);

		$price = array();
		for( $i = 3; $i < 11; $i++ ) {
			$price[$i.'0'] = $i.'0万円以上';
		}
		$this->set('price', $price);
		$this->set('count', $this->Project->find('count'));
		$this->set('skill', $this->Project->Skill->find('list', array('conditions' => array('Skill.id' => array(1,3,5,7)))));
		$this->set('position', $this->Project->Position->find('list', array('conditions' => array('Position.id' => array(1,3,5,7)))));
		$this->set('service', $this->Project->Service->find('list', array('conditions' => array('Service.id' => array(1,3,5,7)))));
		$this->set('web_project', $this->Project->find('all', array('joins' => $join, 'conditions' => $condition, 'limit' => 5, 'order' => 'Project.id DESC')));
	}
}