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
		'Db' => array(
			'className' => 'Db',
			'joinTable' => 'dbs_projects',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'db_id',
			'with' => 'DbsProject',
			'unique' => true,
			'conditions' => 'DbsProject.db_id = Db.id',
			'fields' => array('Db.id', 'Db.name')
		),
		'Framework' => array(
			'className' => 'Framework',
			'joinTable' => 'frameworks_projects',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'framework_id',
			'with' => 'FrameworksProject',
			'unique' => true,
			'conditions' => 'FrameworksProject.framework_id = Framework.id',
			'fields' => array('Framework.id', 'Framework.name')
		),
		'Tool' => array(
			'className' => 'Tool',
			'joinTable' => 'projects_tools',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'tool_id',
			'with' => 'ProjectsTool',
			'unique' => true,
			'conditions' => 'ProjectsTool.tool_id = Tool.id',
			'fields' => array('Tool.id', 'Tool.name')
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
		'PrimarySkill' => array(
			'className' => 'PrimarySkill',
			'foreignKey' => 'primary_skill_id'
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