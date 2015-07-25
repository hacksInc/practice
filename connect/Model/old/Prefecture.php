<?php

class Prefecture extends AppModel {
	public $name = 'Prefecture';
	public $hasOne = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'prefecture_id',
		)
	);
}