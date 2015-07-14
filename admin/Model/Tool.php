<?php

class Tool extends AppModel {

	public $name = 'Tool';
	
	public $hasAndBelongsToMany = array(
		'ProjectsTool' => array(
			'className' => 'Project',
			'joinTable' => 'projects_tools',
			'foreignKey' => 'tool_id',
			'associationForeignKey' => 'project_id',
			'unique' => true
		)
	);
}