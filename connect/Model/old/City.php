<?php

class City extends AppModel {
	public $name = 'City';
	public $hasOne = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'city_id',
		)
	);
}