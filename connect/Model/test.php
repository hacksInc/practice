<?php

class Project extends AppModel {

	public $name = 'Project';
	public $order = 'Project.modified DESC';
	
	public $hasAndBelongsToMany = array(
		'Skill' => array(
			'className' => 'Skill',
			'joinTable' => 'projects_skills',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'skill_id',
			'with' => 'ProjectsSkill',
			'unique' => true,
			'conditions' => array('ProjectsSkill.skill_id = Skill.id', 'ProjectsSkill.project_id = Project_id'),
			'fields' => array('Skill.id', 'Skill.name')
		),
/*		'Contract' => array(
			'className' => 'Contract',
			'joinTable' => 'contracts_projects',
			'foreignKey' => 'project_id',
			'associationForeignKey' => 'contract_id',
			'with' => 'ContractsProject',
			'unique' => true,
			'conditions' => 'ContractsProject.contract_id = Contract.id',
			'fields' => array('Contract.id', 'Contract.name')
		),
*/	);

	public $belongsTo = array(
/*		'Service' => array(
			'className' => 'Service',
			'foreignKey' => 'service_id',
		),
*/		'Position' => array(
			'className' => 'Position',
			'foreignKey' => 'position_id',
		),
/*		'Prefecture' => array(
			'className' => 'Prefecture',
			'foreignKey' => 'prefecture_id',
		),
*/		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id',
		),
		'Liquidation' => array(
			'className' => 'Liquidation',
			'foreignKey' => 'liquidation_id',
		),
		'PrimarySkill' => array(
			'className' => 'PrimarySkill',
			'foreignKey' => 'primary_skill_id',
		),
		'MinPrice' => array(
			'className' => 'MinPrice',
			'foreignKey' => 'min_price_id',
		),
		'MaxPrice' => array(
			'className' => 'MaxPrice',
			'foreignKey' => 'max_price_id',
		),
	);

	public function price() {
		$price = array();
		for( $i = 3; $i < 11; $i++ ) {
			$price[$i.'0'] = $i.'00,000〜';
		}
		return $price;
	}

	public function price_format() {

		$price = array();
		$price_id = $this->MinPrice->find('list', array(
			'conditions' => array('MinPrice.id' => array(3,5,7,9,11,13,15))
		));
		foreach ($price_id as $key => $value) {
			$price += array($key => ($value / 10000).'万円以上' );
		}
		return $price;

	}

	public function sidebar() {

		$sub_conditions = array(
			'Project.primary_skill_id' => array(1,2,3,6,7,8)
		);
	 	$sub_id_list = $this->find('list', array(
	 		'fields' => 'Project.id',
	 		'order' => 'rand()',
	 		'conditions' => $sub_conditions,
	 		'limit' => 4
	 	));
		$sub_fields = array(
			'Project.id', 'Project.title', 'Project.station', 'MinPrice.name','MaxPrice.name', 'Position.name'
		);

	 	$sub_project = $this->find('all', array(
	 		'recursive' => 1,
	 		'fields' => $sub_fields,
	 		'conditions' => array('Project.id' => $sub_id_list),	 		
	 	));

	 	return $sub_project;
	}

	public function pickup() {

		$fields = array(
			'Project.id', 'Project.title', 'Project.station', 'MinPrice.name','MaxPrice.name', 'Position.name'
		);
		
		$pickup = $this->find('all', array(
			'recursive' => 1,
			'fields' => $fields,
			'limit' => 6,
		));

		return $pickup;

	}

	public function same($params){

	 	$project = $this->find('first', array(
	 		'fields' => array('position_id', 'primary_skill_id'),
	 		'conditions' => array('id' => $params),
	 		'limit' => 1
	 	));

		$same_fields = array(
			'Project.id', 'Project.title', 'Project.station', 'MinPrice.name','MaxPrice.name', 'Position.name', 'PrimarySkill.name'
		);

	 	$same_project = $this->find('all',array(
			'recursive' => 1,
			'fields' => $same_fields,
			'limit' => 4,
	 		'conditions' => array(
	 			'OR' => array(
	 				array('Project.position_id' => $project['Project']['position_id']),
	 				array('Project.primary_skill_id' => $project['Project']['primary_skill_id']),
	 			)
	 		)
	 	));

	 	return $same_project;

	}

	public function search($query) {

		$freeword = array();
		$conditions = array();
		$keywords = array();

		// 検索条件にSkillがセットされている場合
		if (isset($query['Skill']) && !empty($query['Skill'])) {

			
			$skill = $this->ProjectsSkill->find('list',array(
				'fields' => 'project_id',
				'conditions' => array('ProjectsSkill.skill_id' => $query['Skill'])
			));

			$conditions = array_merge($conditions, array(
				'Project.id' => $skill
			));

			$keywords = array_merge($keywords, $this->Skill->find('list',array(
				'conditions' => array('Skill.id' => $query['Skill']),
				'fields' => 'Skill.name'
			)));
		}

		// 検索条件にPositionがセットされている場合
		if (isset($query['Position']) && !empty($query['Position'])) {
		
			$conditions = array_merge($conditions, array(
				'Project.position_id' => $query['Position']
			));

			$keywords = array_merge($keywords, $this->Position->find('list',array(
				'conditions' => array('Position.id' => $query['Position']),
				'fields' => 'Position.name'
			)));
		}

		// 検索条件にPriceがセットされている場合
		if (isset($query['price']) && !empty($query['price'])) {
		
			$conditions = array_merge($conditions,array(
				'OR' => array(
					array('Project.min_price_id >' => $query['price']),
					array('Project.max_price_id >' => $query['price'])
				)
			));

			$price_id = $this->MinPrice->find('list',array(
				'conditions' => array('MinPrice.id' => $query['price']),
				'fields' => 'MinPrice.name'
			));
			foreach ($price_id as $key) {
				$price = array(($key / 10000).'万円以上');
			}

			$keywords = array_merge($keywords, $price);
		}

		// 検索条件にfreewordがセットされている場合
		if (isset($query['freeword'])&& !empty($query['freeword'])) {

			$replace = array("　", ",", ".", "/", "¥", "|", "<", ">", "?", "\\", "、","(", ")", "$", "#", "%", "&");
			$keyword = str_replace($replace, " ", $query['freeword']);
			$key_array = explode( " " , $keyword );

			for( $i=0; $i < count($key_array); $i++ ) {
				$title = "Project.title like '%$key_array[$i]%'";
				$content = "Project.content like '%$key_array[$i]%'";
				$station = "station like '%$key_array[$i]%'";
				$primary_id = $this->PrimarySkill->find('list', array(
					'fields' => 'PrimarySkill.id',
					'conditions' => array("PrimarySkill.name like '%$key_array[$i]%'")
				));
				if (!empty($primary_id)){
					$primary_ids = array();
					$primary_ids = array_merge($primary_ids, $primary_id);
					$primary = array("Project.primary_skill_id" => $primary_ids);
					$conditions = array_merge($conditions, array(array("OR" => array($title,$content,$station,$primary))));
				} else {
					$conditions = array_merge($conditions, array(array("OR" => array($title,$content,$station))));
				}

			}
			$keywords = array_merge($keywords, $key_array);
		}
			
		$id_list = $this->find('list', array(
			'fields' => 'Project.id',
			'recursive' => 1,
			'conditions' => $conditions
		));

		return array('id_list' => $id_list, 'keywords' => $keywords);

	}

	public function entry($entry_id) {

		$fields = array(
			'Project.id', 'Project.title', 'Project.station','MinPrice.name','MaxPrice.name','Position.name'
		);

		$entry_project = $this->find('all', array(
			'fields' => $fields,
			'recursive' => 1,
			'conditions' => array('Project.id' => $entry_id),
		));

		return $entry_project;

	}


/*	public function joins() {

		$join = array(
			array(
				'table' => 'projects_skills',
				'alias' => 'ProjectsSkill',
				'type' => 'inner',
				'conditions' => array(
					'Project.id = ProjectsSkill.project_id'),
			),
			array(
				'table' => 'skills',
				'alias' => 'Skill',
				'type' => 'inner',
				'conditions' => array(
					'ProjectsSkill.skill_id = Skill.id'),
			),
			array(
				'table' => 'contracts_projects',
				'alias' => 'ContractsProject',
				'type' => 'inner',
				'conditions' => array(
					'Project.id = ContractsProject.project_id'),
			),
			array(
				'table' => 'contracts',
				'alias' => 'Contract',
				'type' => 'inner',
				'conditions' => array(
					'ContractsProject.contract_id = Contract.id'),
			),
		);
		return $join;
	}
*/
}