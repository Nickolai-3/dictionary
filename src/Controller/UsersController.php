<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['login', 'ping', 'index']);
    }

    public function index() {
        $user = $this->Auth->user();
        $this->set(compact('user'));
        $this->set('_serialize',  ['user']);
    }

   /**
    * Login user
    */
    public function login() {
        $user = $this->Auth->user();
        if (isset($user['id'])) {
            $code = 201;
            $message = 'Ужо увайшлі';
        } else {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                $code = 200;
                $message = __('Вы паспяхова ўвайшлі');
            } else {
                $code = 400;
                $message = __('Неправільнае імя карыстальніка альбо пароль');
            }
        }
        $this->response->statusCode($code);
        $this->set(compact('message', 'user'));
        $this->set('_serialize', ['message', 'user']);
    }

    public function unauthorized() {
        $this->response->statusCode(401);
        $message = __('Not logged in');
        $this->set(compact('message'));
        $this->set('_serialize', ['message']);
    }

    public function logout() {
        $this->Auth->logout();
        $message = 'ok';
        $this->set(compact('message'));
        $this->set('_serialize', ['message']);
    }

    /**
     * Every user should have sid, after authorizing sid is marked as authorized
     */
    public function ping() { 
        $session = $this->request->session();
        $session->start();
        $sid = $session->id();
        $this->set(compact('sid'));
        $this->set('_serialize', ['sid']);
    }
}
