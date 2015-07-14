<?php

class Contract extends AppModel {

	public $name = 'Contract';
	
	public $hasAndBelongsToMany = array(
		'ContractsProject' => array(
			'className' => 'Project',
			'joinTable' => 'contracts_projects',
			'foreignKey' => 'contract_id',
			'associationForeignKey' => 'project_id',
			'unique' => true
		)
	);
}