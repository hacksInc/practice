<?php

class ProjectsController extends AppController {
	 public $uses = array('Project',);

	 public function index() {

		$keep_id = null;
		$keep_count = 0;
		$fields = array(
			'Project.id', 'Project.title', 'Project.station','Project.meeting', 'Project.must_skill', 'Project.content',
			'Liquidation.name','Position.name', 'MinPrice.name','MaxPrice.name',
		);

		if ($this->request->is('get')) {

			$result = $this->Project->search($this->request->query);

			$this->paginate = array(
				'limit' => 10,
				'paramType' => 'querystring',
				'recursive' => 1,
				'conditions' => array('Project.id' => $result['id_list']),
				'fields' => $fields,
			);

		} else {

			$this->paginate = array(
				'limit' => 10,
				'paramType' => 'querystring',
				'recursive' => 1,
				'fields' => $fields,
			);
		}

	 	if ( $this->Session->check('keep_id') && $this->Session->check('keep_count') ) {
	 		$keep_id = $this->Session->read('keep_id');
	 		$keep_count = $this->Session->read('keep_count');
	 	}

	 	$key = implode(',', $result['keywords']);
	 	if (!empty($key)){
	 		$key = $key.'の';
	 	}

	 	$keywords = implode(',', $result['keywords']);

	 	$title = $key."案件/求人一覧 | IT/webフリーランスの案件/求人情報Connect(コネクト) ";
	 	$keywords = $keywords.",フリーランス,案件,求人,仕事";
	 	$discription =  "ITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイトConnect(コネクト)の".$key."案件一覧です。";

		$this->set('title', $title);
		$this->set('keywords', $keywords);
		$this->set('description', $discription);
		$this->set('ogtype', 'article');
		$this->set('ogurl', 'https://connect-job.com/Projects');
		$this->set('css', 'search');
		$this->set('js', 'search');
		$this->set('key', $key);

 		$this->set('keep_id', $keep_id);
 		$this->set('keep_count', $keep_count);
		$this->set('price', $this->Project->price_format());
		$this->set('skills', $this->Project->Skill->find('list', array('limit' => 15)));
		$this->set('positions', $this->Project->Position->find('list'));
		$this->set('project', $this->paginate());
		$this->set('sub_project', $this->Project->sidebar());

	}

	public function detail() {

		$keep_id = array();
		$keep_count = 0;

		// idを取得
	 	$params = $this->params['id'];
	 	$project = $this->Project->find('first', array(
	 		'conditions' => array('Project.id' => $params),
	 		'recursive' => 1,
	 		'limit' => 1
	 	));

	 	$project2 = array();
	 	for($i=0;$i < count($project['Skill']); $i++){
	 		$project2 = array_merge($project2, array($project['Skill'][$i]['id']));
	 	}
//	 	$test = implode(',', $project2);
	 	$project3 = $this->Project->ProjectsSkill->find('list',array(
	 		'fields' => 'DISTINCT ProjectsSkill.project_id',
	 		'conditions' => array('ProjectsSkill.skill_id' => $project2)
	 	));


	 	// もし案件が見つからなければトップページへ飛ばす
	 	if ($project == null) {
	 		// throw new NotFoundException();
	 		return $this->redirect(array('action' => 'index'));
	 	}
	 	
	 	// セッションチェック
	 	if ( $this->Session->check('keep_id') && $this->Session->check('keep_count') ) {
	 		$keep_id = $this->Session->read('keep_id');
	 		$keep_count = $this->Session->read('keep_count');
	 	}

	 	if ( in_array($params, $keep_id) ) {
	 		$keep_id = true;
	 	} else {
	 		$keep_id = false;
	 	}

	 	$skill_id = $this->Project->ProjectsSkill->find('list',array(
	 		'fields' => 'ProjectsSkill.skill_id',
	 		'conditions' => array('ProjectsSkill.project_id' => $project['Project']['id'])
	 	));
	 	$skill_list = $this->Project->Skill->find('list',array(
	 		'fields' => array('Skill.name'),
	 		'conditions' => array('Skill.id' => $skill_id)
	 	));
	 	$key = implode(',', $skill_list);


	 	$title = $project['Project']['title'].' | IT/webフリーランスの案件/求人情報Connect(コネクト)';
	 	$keywords = $key.','.$project['Position']['name'].','.$project['Project']['title'].',フリーランス,案件,求人,仕事';
 		$description = $project['Project']['station'].'にある'.$project['Project']['title'].'の案件、求人です。'.$project['Position']['name'].'の経験や'.$key.'のスキルが活かせる案件、お仕事ならIT/webフリーランスの案件/求人情報を運営するConnect(コネクト)にお任せ下さい。';


 		$this->set('test',$project3);

		$this->set('title', $title);
		$this->set('keywords', $keywords);
		$this->set('description', $description);
		$this->set('ogtype', 'article');
		$this->set('ogurl', 'https://connect-job.com/projects/'.$project['Project']['id']);
		$this->set('css', 'project');
		$this->set('js', 'project');

		$this->set('keep_count', $keep_count);
		$this->set('keep_id', $keep_id);
	 	$this->set('project', array($project));
	 	$this->set('sub_project',  $this->Project->same($params));
	}

	public function home() {

		$keep_id = null;
		$keep_count = 0;

	 	if ( $this->Session->check('keep_id') && $this->Session->check('keep_count') ) {
	 		$keep_id = $this->Session->read('keep_id');
	 		$keep_count = $this->Session->read('keep_count');
	 	}

		$this->set('title', 'Connect(コネクト) IT/webフリーランスの案件/求人情報');
		$this->set('keywords', 'フリーランス,案件,求人,仕事,エンジニア,デザイナー,web,IT');
		$this->set('description', 'Connect(コネクト)はITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイト。キャリア相談〜案件紹介、アフターフォローまでIT/webフリーランスをトータルサポート！');
		$this->set('ogtype', 'website');
		$this->set('ogurl', 'https://connect-job.com');
		$this->set('css', 'home');
		$this->set('js', 'home');

	 	$this->set('keep_count', $keep_count);
		$this->set('price', $this->Project->price_format());
		$this->set('skills', $this->Project->Skill->find('list', array('limit' => 15)));
		$this->set('positions', $this->Project->Position->find('list'));
		$this->set('pickup_project', $this->Project->pickup());
		$this->set('sub_project', $this->Project->sidebar());
	}

}