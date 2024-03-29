<?php
namespace App\Controller\Api\Posts;

use App\Controller\Api\AppController;
use App\Model\Entity\Like;
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
     * [GET]
     * [PRIVATE]
     * 
     * Fetches a single post
     * includes likers (user_id)
     * and count of comments
     * 
     * @return object - Post Entity/s
     */
    public function view()
    {
        $this->request->allowMethod('get');
        $postId = (int) $this->request->getParam('id');
        $data = $this->Posts->fetchPost($postId);
        return $this->responseData($data);
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
     * [POST]
     * [PRIVATE]
     * 
     * Shares a post
     * 
     * @return object - Post Entity
     */
    public function share()
    {
        $this->request->allowMethod('post');
        $postId = (int) $this->request->getParam('id');
        $userId = (int) $this->Auth->user('id');
        $requestData = $this->request->getData();
        $post = $this->Posts->sharePost($postId, $userId, $requestData);
        if ($post->hasErrors()) {
            return $this->responseUnprocessableEntity($post->errors());
        }

        $this->loadComponent('PostHandler');
        $this->PostHandler->notifyAfterShare($post);

        return $this->responseCreated($post);
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
        $like = $this->Posts->Likes->toggleLike($userId, $postId);
        $totalCount = $this->Posts->Likes->countByPost($postId);

        if ($like instanceof Like) {
            $this->loadComponent('PostHandler');
            $this->PostHandler->notifyAfterLike($like);
        }

        return $this->responseCreated(['totalCount' => $totalCount]);
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
        $page = $this->request->getQuery('page', 1);
        return $this->responseData(
            $this->Posts->fetchPostsForLanding(
                $this->Auth->user('id'),
                $page
            )
        );
    }

    /**
     * [POST]
     * [PRIVATE]
     * 
     * Add a comment to a post
     * 
     * @param int $postId - posts.id
     * 
     * @return int Comment Count
     */
    public function addComment()
    {
        $this->request->allowMethod('post');
        $postId = $this->request->getParam('id');
        $comment = $this->Posts->Comments->addCommentToPost(
            $postId,
            $this->Auth->user('id'),
            $this->request->getData()
        );

        if ($comment->hasErrors()) {
            return $this->responseUnprocessableEntity($comment->errors());
        }

        $this->loadComponent('PostHandler');
        $this->PostHandler->notifyAfterComment($comment);

        return $this->responseCreated([
            'commentCount' => $this->Posts->Comments->countPerPost($postId)
        ]);
    }

    /**
     * [GET]
     * [PRIVATE]
     * 
     * TODO transfer to Posts/LikesController
     * 
     * Fetches users who liked of a post
     * 
     * @param int $postId - posts.id
     * 
     * @return array of Users
     */
    public function fetchLikers()
    {
        $this->request->allowMethod('get');
        $postId = $this->request->getParam('id');
        $page = $this->request->getQuery('page', 1);
        $result = $this->Posts->Likes
            ->fetchByPost($postId, $page)
            ->disableHydration()
            ->toList();

        return $this->responseData($result);
    }

    /**
     * [GET]
     * [PRIVATE]
     * 
     * Fetches comments of a post
     * 
     * @param int $postId - posts.id
     * 
     * @return array of Comments
     */
    public function fetchComments()
    {
        $this->request->allowMethod('get');
        $postId = $this->request->getParam('id');
        $page = $this->request->getQuery('page', 1);
        $result = $this->Posts->Comments
            ->fetchPerPost($postId, $page)
            ->toList();
        return $this->responseData([
            'list' => $result,
            'totalCount' => $this->Posts->Comments->countPerPost($postId)
        ]);
    }

    /**
     * [GET]
     * [PRIVATE]
     * 
     * Fetches the posts to be displayed on users page
     * 
     * @param string username
     * 
     * @return array - list of posts
     */
    public function fetchPostsOfUser()
    {
        $this->request->allowMethod('get');
        $page = $this->request->getQuery('page', 1);
        $username = $this->request->getParam('username');
        $userId = $this->Posts->Users->fetchByUsername($username, ['id'])->id;
        return $this->responseData(
            $this->Posts->fetchPostsForUser(
                $userId,
                $page
            )
        );
    }
}
