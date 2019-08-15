<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;

require_once ROOT . DS . 'vendor' . DS . 'UploadErrorMessage.php';
require_once ROOT . DS . 'vendor' . DS . 'UniqueFileName.php';

/**
 * Images Controller
 *
 * @property \App\Model\Table\ImagesTable $Images
 */
class ImagesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $dir = dir( $this->getPath() );
        $images = [];
        while($entry = $dir->read()) {
            if (!in_array($entry, ['.', '..']))
            $images[] = $entry;
        }
        $dir->close();
        sort($images);
        $this->set(compact('images'));
        $this->set('_serialize', ['images']);
    }

    
    /**
     * Create new model  (with already uploaded file)
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function create()
    {
        $imageRequest = $this->request->input('json_decode', true);
        $this->loadModel('Words');
        if (!isset($imageRequest['word_id'])) {
            $word = $this->Words->newEntity();
            $this->Words->save($word);
            $imageRequest['word_id'] = $word['id'];
        }
        $imageEntity = $this->Images->newEntity($imageRequest);
        if ($this->Images->save($imageEntity))
        {
            $message = __('The image has been linked to the word.');
        } else {
            $this->response->statusCode(400);
            $message = __('The image could not be linked. Please, try again.');
        }
        $errors = $imageEntity->errors();
        $word = $this->getWord($imageRequest['word_id']);
        $this->set(compact('word', 'errors', 'message'));
        $this->set('_serialize', ['word', 'errors', 'message']);
    }

    /**
     * Upload an image
     *
     * @param string|null $id Image id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function upload()
    {
        $files = $this->request->data('file');
        $word_id = $this->request->data('word_id');
        $errors = [];
        $images = [];
        foreach($files as $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $toName = $this->getUniqueFileName()->buildName($file['name']);
                if (move_uploaded_file($file['tmp_name'],  $this->getPath()  . $toName)) {
                    $successfullyUploaded[] = $toName;
                    
                    if (!$word_id) {
                        $this->loadModel('Words');
                        $word = $this->Words->newEntity();
                        $this->Words->save($word);
                        $word_id = $word['id'];
                    }

                    $modelData = ['word_id' => $word_id, 'image' => $toName ];

                    $image = $this->Images
                        ->find()
                        ->where( $modelData )
                        ->first();
                    if (!$image) {
                        $image = $this->Images->newEntity();
                    }

                    $this->Images->patchEntity($image, $modelData);
                    if ($this->Images->save($image)) {
                        array_push($images, $image);
                    }
                } else {
                    $errors[] = [
                        'message' => __("File was uploaded, but unable to move"),
                        'file' => $file['name'],
                    ];
                }
            } else {
                $errors[] = [
                    'message' => (string) new \UploadErrorMessage($file['error']),
                    'file' => $file['name']
                ];
            }
        }
        $word = $this->getWord($word_id);
        $this->set(compact('errors', 'images', 'word'));
        $this->set('_serialize', ['errors', 'images', 'word']);
    }

    /**
     * Delete method
     *
     * @param string|null $name Image name.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function unlinkFile($name)
    {
        $path =  $this->getPath()  . $name;
        if (!file_exists($path)) {
            $this->response->statusCode(404);
            $message = __('File does not exists');
        } else {
            if (unlink($path)) {
                $message = __('The image has been deleted.');
            } else {
                $this->response->statusCode(409);
                $message = __('The image could not be deleted. Please, try again.');
            }
        }
        $this->set(compact('message'));
        $this->set('_serialize', ['message']);
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
        $image = $this->Images->get($id);
        $word_id = $image['word_id'];
        if ($this->Images->delete($image)) {
            $message = __('The image has been unlinked from word.');
        } else {
            $this->response->statusCode(400);
            $message = __('The image could not be unlinked. Please, try again.');
        }
        $word = $this->getWord($word_id);
        $this->set(compact('message', 'word'));
        $this->set('_serialize', ['message', 'word']);
    }

    protected function getWord($id) {
        $this->loadModel('Words');
        return $this->Words->find()
            ->where(['id' => $id])
            ->contain(['Images', 'WordsTranslations'])
            ->first();
    }

    /**
     * @return \UniqueFileName
     */
    protected function getUniqueFileName() {
        return new \UniqueFileName( $this->getPath() );
    }

    /**
     * 
     * @return string path to images
     */
    private function getPath() {
        return Configure::read('App.imgPath');
    }
}
