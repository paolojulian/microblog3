<?php
namespace App\Controller\Api\Posts;

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
    /**
     * [GET]
     * [PRIVATE]
     * 
     * Fetches the posts to be displayed in the landing page
     * 
     * @return array - list of posts
     */
    public function fetchPosts()
    {
        $this->request->allowMethod('get');
        if ( ! $pageNo = $this->request->getParam('pageNo')) {
            $pageNo = 1;
        }
        return $this->responseData(
            $this->Posts->fetchPostsForLanding(
                $this->Auth->user('id'),
                $pageNo
            )
        );
    }
}
