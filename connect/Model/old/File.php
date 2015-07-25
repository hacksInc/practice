<?php

class File extends AppModel {
	
	public $name = 'File';

	public $hasOne = array(
		'Member' => array(
			'className' => 'Member',
			'foreignKey' => 'file_id'
		)
	);
}