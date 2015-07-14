<?php

class Liquidation extends AppModel {
	public $name = 'Liquidation';
	public $hasOne = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'liquidation_id',
		)
	);
}