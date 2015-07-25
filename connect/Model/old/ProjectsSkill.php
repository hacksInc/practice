<?php
class ProjectsSkill extends AppModel {

	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id'
		),
		'Skill' => array(
			'className' => 'Skill',
			'foreignKey' => 'skill_id'
		)
	);
}