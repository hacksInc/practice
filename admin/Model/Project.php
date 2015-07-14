<?php

class Project extends AppModel {

	public $name = 'Project';
	
	public $hasAndBelongsToMany = array(
		'Skill' => array(
			'className' => 'Skill',
			'joinTable' => 'projects_skills',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'skill_id',
			'with' => 'ProjectsSkill',
			'unique' => true
		),
		'Db' => array(
			'className' => 'Db',
			'joinTable' => 'dbs_projects',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'db_id',
			'with' => 'DbsProject',
			'unique' => true
		),
		'Framework' => array(
			'className' => 'Framework',
			'joinTable' => 'frameworks_projects',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'framework_id',
			'with' => 'FrameworksProject',
			'unique' => true
		),
		'Tool' => array(
			'className' => 'Tool',
			'joinTable' => 'projects_tools',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'tool_id',
			'with' => 'ProjectsTool',
			'unique' => true
		),
		'Contract' => array(
			'className' => 'Contract',
			'joinTable' => 'contracts_projects',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'contract_id',
			'with' => 'ContractsProject',
			'unique' => true
		),
	);

	public $belongsTo = array(
		'Service' => array(
			'className' => 'Service',
			'foreignKey' => 'service_id'
		),
		'Position' => array(
			'className' => 'Position',
			'foreignKey' => 'position_id'
		),
		'Prefecture' => array(
			'className' => 'Prefecture',
			'foreignKey' => 'prefecture_id'
		),
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id'
		),
		'Liquidation' => array(
			'className' => 'Liquidation',
			'foreignKey' => 'liquidation_id'
		),
	);

	public $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'message' => '入力してください！'
			),
		'body' => array(
			'rule' => 'notEmpty'
			)
		);
}