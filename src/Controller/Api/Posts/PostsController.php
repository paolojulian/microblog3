<?php
namespace App\Controller\Api\Posts;

use App\Controller\Api\AppController;
use Cake\Event\Event;

/**
 * Api/Post/Posts Controller
 *
 *
 * @method \App\Model\Entity\Api/Post/Post[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PostsController extends AppController
{
    public function isAuthorized()
    {
        if ( ! in_array($this->request->getParam('action'), ['delete', 'update'])) {
            return true;
        }

        if ( ! parent::isOwnedBy($this->Posts, $this->Auth->user('id'))) {
            return false;
        }

        return parent::isAuthorized();
    }
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
            $requestData['img_path'] = $this->PostHandler->uploadImage($requestData['img']);
        }

        $requestData['user_id'] = $this->Auth->user('id');
        $post = $this->Posts->addPost($requestData);
        if (true !== $post['status']) {
            return $this->responseUnprocessableEntity($post['errors']);
        }
        return $this->responseCreated($post['entity']);
    }

    /**
     * TODO: Investigate why PUT does not get FormData()
     * [POST]
     * [PRIVATE]
     * 
     * Updates a Post
     * 
     * @return status 200
     */
    public function update()
    {
        $this->request->allowMethod('post');
        $this->loadComponent('PostHandler');
        $postId = (int) $this->request->getParam('id');
        // Upload Image first
        $requestData = $this->request->getData();
        if (isset($requestData['img']) && !!$requestData['img']) {
            $requestData['img_path'] = $this->PostHandler->uploadImage($requestData['img']);
        }

        $requestData['user_id'] = $this->Auth->user('id');
        $post = $this->Posts->updatePost($postId, $requestData);
        if ($post->hasErrors()) {
            return $this->responseUnprocessableEntity($post->errors());
        }
        return $this->responseOk($post);
    }

    /**
     * [DELETE]
     * [PRIVATE]
     * 
     * Updates a Post
     * 
     * @return status 204
     */
    public function delete()
    {
        $this->request->allowMethod('delete');
        $postId = (int) $this->request->getParam('id');
        $this->Posts->deletePost($postId);
        return $this->responseDeleted();
    }

    /**
     * [PATCH]
     * [PRIVATE]
     * 
     * Toggle likes a Post
     * 
     * @return int - number of likes of the post
     */
    public function like()
    {
        $this->request->allowMethod('patch');
        $postId = (int) $this->request->getParam('id');
        $userId = (int) $this->Auth->user('id');
        $this->Posts->Likes->toggleLike($userId, $postId);
        $totalCount = $this->Posts->Likes->countByPost($postId);
        return $this->responseData(['totalCount' => $totalCount]);
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
