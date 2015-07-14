<?php

class Framework extends AppModel {

	public $name = 'Framework';
	
	public $hasAndBelongsToMany = array(
		'FrameworksProject' => array(
			'className' => 'Project',
			'joinTable' => 'frameworks_projects',
			'foreignKey' => 'framework_id',
			'associationForeignKey' => 'project_id',
			'unique' => true
		)
	);
}