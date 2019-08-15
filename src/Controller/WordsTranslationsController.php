<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * WordsTranslations Controller
 *
 * @property \App\Model\Table\WordsTranslationsTable $WordsTranslations
 * @property \App\Model\Table\WordsTable $Words
 */
class WordsTranslationsController extends AppController
{

    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['index', 'view']);
    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index($lang)
    {
        $query = $this->WordsTranslations->find()
            ->select(['id', 'name', 'word_id'])
            ->contain('Words')
            ->where(compact('lang'))
            ->orderAsc('name');
        $words = $this->paginate($query);
        $this->set(compact('words'));
        $this->set('_serialize', ['words']);
    }

    /**
     * View method
     *
     * @param string|null $name Word id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($name)
    {
        $word = $this->WordsTranslations
            ->find()
            ->where(compact('name'))
            ->contain(['Words' => ['Images']])
            ->first();
        $message = '';
        if (!$word) {
            $message = __('Word not found');
            $this->response->statusCode(404);
        }
        $this->set(compact('word', 'message'));
        $this->set('_serialize', ['word', 'message']);
    }

    public function getByLang($lang, $word_id) {
        $word = $this->WordsTranslations
            ->find()
            ->where(compact('lang', 'word_id'))
            ->contain(['Words' => ['Images']])
            ->first();
        $message = '';
        if (!$word) {
            $message = __('Word not found');
            $this->response->statusCode(404);
        }
        $this->set(compact('word', 'message'));
        $this->set('_serialize', ['word', 'message']);
    }

    /**
     * Create new
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function create()
    {
        $wordRequest = $this->request->input('json_decode', true);
        $this->loadModel('Words');
        if (!isset($wordRequest['word_id'])) {
            $word = $this->Words->newEntity();
            $this->Words->save($word);
            $wordRequest['word_id'] = $word['id'];
        }
        $wordsTranslations = $this->WordsTranslations->newEntity($wordRequest);
        if ($this->WordsTranslations->save($wordsTranslations))
        {
            $message = __('The word has been saved.');
        } else {
            $this->response->statusCode(400);
            $message = __('The word could not be saved. Please, try again.');
        }
        $errors = $wordsTranslations->errors();
        $word = $this->getWord($wordRequest['word_id']);
        $this->set(compact('word', 'errors', 'message'));
        $this->set('_serialize', ['word', 'errors', 'message']);
    }
    
    /**
     * Update existed
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function update()
    {
        $wordRequest = $this->request->input('json_decode', true);
        $id = $wordRequest['id'];
        $wordsTranslations = $this->WordsTranslations->get($id);
        if (isset($wordRequest['created'])) {
            unset($wordRequest['created']);
        }
        if (isset($wordRequest['modified'])) {
            unset($wordRequest['modified']);
        }
        $this->WordsTranslations->patchEntity($wordsTranslations, $wordRequest);
        if ($this->WordsTranslations->save($wordsTranslations))
        {
            $message = __('The word has been saved.');
        } else {
            $this->response->statusCode(400);
            $message = __('The word could not be saved. Please, try again.');
        }
        $errors = $wordsTranslations->errors();
        $word = $this->getWord($wordsTranslations['word_id']);
        $this->set(compact('word', 'errors', 'message'));
        $this->set('_serialize', ['word', 'errors', 'message']);
    }
    
    /**
     * 
     * @param int $id guid
     * @return \App\Model
     */
    protected function getWord($id) {
        $this->loadModel('Words');
        return $this->Words->find()
            ->where(['id' => $id])
            ->contain(['Images', 'WordsTranslations'])
            ->first();
    }

}
