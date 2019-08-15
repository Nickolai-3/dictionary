<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * REST Words Controller
 * 
 * GET words
 * GET words/:id
 * POST words - creates new word, or update existing if id presents in request
 * DELETE words/:id
 * 
 * @property \App\Model\Table\WordsTable $Words
 * @property \App\Model\Table\WordsTranslationsTable $WordsTranslations
 */
class WordsController extends AppController
{

    public function fullTranslation($id) {
        $word = $this->Words
            ->find()
            ->where(compact('id'))
            ->contain(['Images', 'WordsTranslations'])
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
     * Delete method
     *
     * @param string|null $id Word id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $word = $this->Words->get($id);
        if ($this->Words->delete($word)) {
            $message = __('The word has been deleted.');
        } else {
            $this->response->statusCode(400);
            $message = __('The word could not be deleted. Please, try again.');
        }
        $this->set(compact('message'));
        $this->set('_serialize', ['message']);
    }
}
