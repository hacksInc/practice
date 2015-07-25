<?php

class Project extends AppModel {

	public $name = 'Project';
	public $order = 'Project.modified DESC';
	
	public $hasAndBelongsToMany = array(
		'Skill' => array(
			'className' => 'Skill',
			'joinTable' => 'projects_skills',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'skill_id',
			'with' => 'ProjectsSkill',
			'unique' => true,
			'conditions' => 'ProjectsSkill.skill_id = Skill.id',
			'fields' => array('Skill.id', 'Skill.name')
		),
		'Contract' => array(
			'className' => 'Contract',
			'joinTable' => 'contracts_projects',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'contract_id',
			'with' => 'ContractsProject',
			'unique' => true,
			'conditions' => 'ContractsProject.contract_id = Contract.id',
			'fields' => array('Contract.id', 'Contract.name')
		),
	);

	public $belongsTo = array(
		'Service' => array(
			'className' => 'Service',
			'foreignKey' => 'service_id',
		),
		'Position' => array(
			'className' => 'Position',
			'foreignKey' => 'position_id',
		),
		'Prefecture' => array(
			'className' => 'Prefecture',
			'foreignKey' => 'prefecture_id',
		),
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id',
		),
		'Liquidation' => array(
			'className' => 'Liquidation',
			'foreignKey' => 'liquidation_id',
		),
		'PrimarySkill' => array(
			'className' => 'PrimarySkill',
			'foreignKey' => 'primary_skill_id',
		),
	);

	public function price() {
		$price = array();
		for( $i = 3; $i < 11; $i++ ) {
			$price[$i.'0'] = $i.'0万円以上';
		}
		return $price;
	}

/*	public function joins() {

		$join = array(
			array(
				'table' => 'projects_skills',
				'alias' => 'ProjectsSkill',
				'type' => 'inner',
				'conditions' => array(
					'Project.id = ProjectsSkill.project_id'),
			),
			array(
				'table' => 'skills',
				'alias' => 'Skill',
				'type' => 'inner',
				'conditions' => array(
					'ProjectsSkill.skill_id = Skill.id'),
			),
			array(
				'table' => 'contracts_projects',
				'alias' => 'ContractsProject',
				'type' => 'inner',
				'conditions' => array(
					'Project.id = ContractsProject.project_id'),
			),
			array(
				'table' => 'contracts',
				'alias' => 'Contract',
				'type' => 'inner',
				'conditions' => array(
					'ContractsProject.contract_id = Contract.id'),
			),
		);
		return $join;
	}
*/
}