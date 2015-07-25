<?php

class Db extends AppModel {

	public $name = 'Db';
	
	public $hasAndBelongsToMany = array(
		'DbsProject' => array(
			'className' => 'Project',
			'joinTable' => 'dbs_projects',
			'foreignKey' => 'db_id',
			'associationForeignKey' => 'project_id',
			'unique' => true
		)
	);
}