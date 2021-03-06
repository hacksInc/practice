<?php

class Skill extends AppModel {

	public $name = 'Skill';
	
	public $hasAndBelongsToMany = array(
		'Project' => array(
			'className' => 'Project',
			'joinTable' => 'projects_skills',
			'foreignKey' => 'skill_id',
			'associationForeignKey' => 'project_id',
		)
	);
}