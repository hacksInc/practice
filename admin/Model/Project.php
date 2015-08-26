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
//			'conditions' => 'ProjectsSkill.skill_id = Skill.id',
			'fields' => array('Skill.id', 'Skill.name')
		),
		'Contract' => array(
			'className' => 'Contract',
			'joinTable' => 'contracts_projects',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'contract_id',
			'with' => 'ContractsProject',
			'unique' => true,
//			'conditions' => 'ContractsProject.contract_id = Contract.id',
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
		'MinPrice' => array(
			'className' => 'MinPrice',
			'foreignKey' => 'min_price_id',
		),
		'MaxPrice' => array(
			'className' => 'MaxPrice',
			'foreignKey' => 'max_price_id',
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