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
				'limit' => 15,
				'paramType' => 'querystring',
				'recursive' => 1,
				'conditions' => array('Project.id' => $result['id_list']),
				'fields' => $fields,
			);

		} else {

			$this->paginate = array(
				'limit' => 15,
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
	 	} else {
	 		$key = $key.'フリーランスの';
	 	}

	 	$keywords = implode(',', $result['keywords']);
	 	if(!empty($keywords)){
	 		$keywords = $keywords.',';
	 	}

	 	$h1 = $key."案件/仕事一覧 | IT/web業界のフリーランス向け案件情報";
	 	$title = $key."案件/仕事一覧 | IT/web業界のフリーランス向け案件/仕事情報 ";
	 	$keywords = $keywords."フリーランス,案件,仕事";
	 	$discription =  $key."案件/仕事一覧です。".$key."経験やスキルが活かせる案件ならフリーエンジニア・デザイナーのフリーランス向け案件/仕事情報を運営するConnect(コネクト)へ";

	 	$this->set('h1', $h1);
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
		$this->set('skills', $this->Project->skills());
		$this->set('positions', $this->Project->positions());
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

	 	$skill_id = $this->Project->ProjectsSkill->find('list',array(
	 		'fields' => 'ProjectsSkill.skill_id',
	 		'conditions' => array('ProjectsSkill.project_id' => $project['Project']['id'])
	 	));
	 	$skill_list = $this->Project->Skill->find('list',array(
	 		'fields' => array('Skill.name'),
	 		'conditions' => array('Skill.id' => $skill_id)
	 	));
	 	$key = implode(',', $skill_list);


	 	$h1 = $project['Project']['title'].' | '.$project['PrimarySkill']['name'].'のフリーランス向け案件';
	 	$title = $project['Project']['title'].' | '.$project['PrimarySkill']['name'].'のフリーランス向け案件/仕事情報';
	 	$keywords = $project['PrimarySkill']['name'].','.$project['Position']['name'].','.$project['Project']['title'].',フリーランス,案件,仕事';
 		$description = $project['Project']['station'].'にあるフリーランス向け'.$project['Project']['title'].'の案件です。'.$project['Position']['name'].'の経験や'.$key.'のスキルが活かせるフリーランス案件、仕事情報ならConnect(コネクト)へ！';

 		$this->set('h1', $h1);
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

	 	$this->set('h1', 'IT/web業界のフリーランスと企業をつなぐ案件/仕事情報');
		$this->set('title', 'Connect(コネクト) - IT/web業界のフリーランスと企業をつなぐフリーエンジニア・デザイナー向け案件/仕事情報');
		$this->set('keywords', 'フリーランス,フリーエンジニア,フリーデザイナー,案件,仕事,web,IT');
		$this->set('description', 'IT/web業界のフリーランスと企業をつなぐフリーエンジニア・デザイナー向け案件/仕事情報サイトConnect(コネクト)。キャリア相談〜案件紹介、アフターフォローまでフリーランスをサポート！');
		$this->set('ogtype', 'website');
		$this->set('ogurl', 'https://connect-job.com');
		$this->set('css', 'home');
		$this->set('js', 'home');

		$this->set('keep_id', $keep_id);
	 	$this->set('keep_count', $keep_count);
		$this->set('price', $this->Project->price_format());
		$this->set('skills', $this->Project->skills());
		$this->set('positions', $this->Project->positions());
		$this->set('pickup_project', $this->Project->pickup());
		$this->set('sub_project', $this->Project->sidebar());
	}

}