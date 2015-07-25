<?php

class Service extends AppModel {
	public $name = 'Service';
	public $hasOne = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'service_id',
		)
	);
}