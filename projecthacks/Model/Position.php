<?php

class Position extends AppModel {

	public $name = 'Position';

	public $hasOne = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'position_id'
		)
	);
}