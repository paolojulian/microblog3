<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\ORM\TableRegistry;

/**
 * Api/Search Controller
 */
class SearchController extends AppController
{

    /**
     * [GET]
     * [PRIVATE] - for logged in users only
     * 
     * The main search function
     * searches for posts and users
     */
    public function index()
    {
        $this->request->allowMethod('get');
        $text = $this->request->getQuery('text', '');
        $page = $this->request->getQuery('page', 1);
        $this->UserModel = TableRegistry::getTableLocator()->get('Users');
        $this->PostModel = TableRegistry::getTableLocator()->get('Posts');
        $this->responseData([
            'users' => $this->UserModel->searchUser($text, $page),
            'posts' => $this->PostModel->searchPosts($text, $page)
        ]);
    }

    /**
     * [GET]
     * [PRIVATE] - for logged in users only
     * 
     * Searches for users by given text
     */
    public function users()
    {
        $this->request->allowMethod('get');
        $text = $this->request->getQuery('text', '');
        $page = $this->request->getQuery('page', 1);
        $this->UserModel = TableRegistry::getTableLocator()->get('Users');
        $this->responseData($this->UserModel->searchUser($text, $page));
    }

    /**
     * [GET]
     * [PRIVATE] - for logged in users only
     * 
     * Searches posts by given text
     */
    public function posts()
    {
        $this->request->allowMethod('get');
        $text = $this->request->getQuery('text', '');
        $page = $this->request->getQuery('page', 1);
        $this->PostModel = TableRegistry::getTableLocator()->get('Posts');
        $this->responseData($this->PostModel->searchPosts($text, $page));
    }

    /**
     * 
     */
    public function test()
    {
        $this->request->allowMethod('get');
        return $this->responseOk();
    }
}
