<?php
namespace App\Controller\Api\Post;

use App\Controller\Api\AppController;

/**
 * Api/Post/Posts Controller
 *
 *
 * @method \App\Model\Entity\Api/Post/Post[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PostsController extends AppController
{
    /**
     * [POST]
     * [PRIVATE]
     * 
     * Creates a Post
     * 
     * @return status 201 - created
     */
    public function create()
    {
        $this->request->allowMethod('post');
        $this->loadComponent('PostHandler');
        // Upload Image first
        $requestData = $this->request->getData();
        if (isset($requestData['img'])) {
            $this->PostHandler->uploadImage($requestData['img']);
        }

        $requestData['user_id'] = $this->Auth->user('id');
        $post = $this->Posts->addPost($requestData);
        if (true !== $post['status']) {
            return $this->responseUnprocessableEntity($post['errors']);
        }
        return $this->responseCreated($post['entity']);
    }
}
