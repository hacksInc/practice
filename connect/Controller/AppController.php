<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	//public $components = array('DebugKit.Toolbar');
	public $components = array('RequestHandler', 'Session');

	public function appError($error) {
// 		$this->redirect(array('controller' => 'Projects', 'action' => 'home'));
	}
	
	public function beforeFilter() {
/*	    $this->Security->blackHoleCallback = 'forceSSL';
	    $this->Security->requireSecure();
	    $this->Security->validatePost = false;
	    $this->Security->csrfUseOnce = false;
		$this->Security->csrfExpires = '+1 hour';
		if ($this->request->is('ajax')) {
			$this->Security->csrfCheck = false;
		}
	    if (isset($this->request->url)) {
	        $uri = $this->request->url;
	        if (!empty($uri) && substr($uri, -1) == '/') {
	            $this->redirect('/' . preg_replace('/(.*?)\/\z/', '$1', $uri), 301);
	        }
	    }
*/	}
	 
	public function forceSSL() {
//	    return $this->redirect('https://' . env('SERVER_NAME') . $this->here);
	}
	
}
