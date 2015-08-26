<?php

class SitemapsController extends AppController {

	public $uses = array('Project');
    public $layout = '';
	
    public function index() {
        $projects = array(
        	'order' => array('Project.modified' => 'desc'),
        	'fields' => array('Project.id', 'Project.modified',),
        );

        $skills = array(
            'fields' => array('Skill.id'),
        );

        $positions = array(
            'fields' => array('Position.id'),
        );

        $home = $this->Project->find('first',array(
            'order' => array('Project.modified' => 'desc'),
            'fields' => array('Project.modified'),
            'limit' => 1
        ));

        $this->set('home', $home);
        $this->set('projects', $this->Project->find('all',$projects));
        $this->set('skills', $this->Project->Skill->find('all',$skills));
        $this->set('positions', $this->Project->Position->find('all',$positions));
        $this->layout = "/xml/default";
        $this->RequestHandler->respondAs('xml');
    }

}