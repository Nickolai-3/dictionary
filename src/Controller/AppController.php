<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see http://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');
       $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => ['username' => 'nick', 'password' => 'password']
                ],
            ],
            'unauthorizedRedirect' => false,
            'loginAction' => 'login' // this does not make much sense, but has to be because auth component always checks if requested url === loginAction
        ]);
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }
    
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow('options');
        
        $this->response->header('Access-Control-Allow-Credentials', 'true');
        $this->response->header('Access-Control-Allow-Origin', 'http://slounik.dojlid.by');
        $this->response->header('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE');
        $this->set('_jsonOptions',  JSON_UNESCAPED_UNICODE);
        $this->RequestHandler->renderAs($this, 'json');
        if (!$this->request->isAjax()) {
            return;
        }
        $sid = $this->request->header('sid');
        if ($sid && self::isValidSid($sid)) {
            session_id($sid);
            $sessionName = Configure::read("Session.cookie");
            session_start($sessionName);
        }
    }

    public function options() {
        $this->response->header('Access-Control-Allow-Headers', 'Content-Type,sid,X-Requested-With');
        $this->set('_serialize', []);
    }
    
    protected static function isValidSid($sid) {
        return preg_match('/^[a-zA-Z0-9]{32}$/', $sid);
    }
}
