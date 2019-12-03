<?php
namespace App\Controller\Api\Users;

use App\Controller\Api\AppController;
use App\Model\Entity\Follower;

/**
 * Api/User/Users Controller
 * @author Pipz <paolovincent.yns@gmail.com>
 * @license mit
 */

class UsersController extends AppController
{

    /**
     * [GET]
     * [PRIVATE]
     * 
     * Fetches the profile
     * of the given username
     * 
     * @return App\Model\Entity\User
     */
    public function profile()
    {
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        $profile = $this->Users->fetchByUsername($username);
        return $this->responseData($profile);
    }

    /**
     * [PUT]
     * [PRIVATE]
     * 
     * Updates the profile of the current user logged in
     * 
     * @return \App\Model\Entity\User
     */
    public function updateUser()
    {
        $this->request->allowMethod('put');
        $user = $this->Users->updateUser(
            $this->Auth->user('id'),
            $this->request->getData()
        );
        if ($user->hasErrors()) {
            return $this->responseUnprocessableEntity($user->errors());
        }
        return $this->responseData($user);
    }

    /**
     * [PATCH]
     * [POST] - for loggedin users only
     * 
     * Edits the image of the current user
     * 
     * @return Status
     */
    public function updateImage()
    {
        $this->request->allowMethod('post');
        $this->loadComponent('UserHandler');
        $requestData = $this->request->getData();
        $avatarUrl = $this->UserHandler->uploadImage(
            $requestData['profile_img'],
            $this->Auth->user('id')
        );
        $this->Users->updateAvatar(
            $this->Auth->user('id'),
            $avatarUrl
        );
        $this->responseOk();
    }

    /**
     * [GET]
     * [PRIVATE]
     * Fetches followed users who followed the given user
     * 
     * @return array - of User
     */
    public function mutual()
    {
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        $page = $this->request->getQuery('page', 1);

        $friendId = (int) $this->Users->fetchByUsername($username, ['id'])->id;
        $users = $this->Users->fetchFriendsWhoFollowedUser(
            $friendId,
            (int) $this->Auth->user('id'),
            $page
        );
        return $this->responseData($users);
    }

    /**
     * [GET]
     * [PRIVATE]
     * Fetches the followers of passed user
     * 
     * @return array
     */
    public function fetchFollowers()
    {
        $this->request->allowMethod('get');
        $id = $this->request->getParam('id');
        $page = $this->request->getQuery('page', 1);
        return $this->responseData(
            $this->Users->Followers->fetchFollowers($id, $page)
        );
    }

    /**
     * [GET]
     * [PRIVATE]
     * Fetches the users being followed by the given user
     * 
     * @return array
     */
    public function fetchFollowing()
    {
        $this->request->allowMethod('get');
        $id = $this->request->getParam('id');
        $page = $this->request->getQuery('page', 1);
        $users = $this->Users->Followers->fetchFollowing($id, $page);
        return $this->responseData($users);
    }

    /**
     * [GET]
     * [PRIVATE]
     * Fetches recommended users of the current user logged in
     * 
     * @return object
     */
    public function recommended()
    {
        $this->request->allowMethod('get');
        $page = $this->request->getQuery('page', 1);
        $users = $this->Users->fetchRecommendedUsers($this->Auth->user('id'), $page);
        $totalCount = $this->Users->countRecommendedUsers($this->Auth->user('id'));
        $data = [
            'list' => $users,
            'totalCount' => $totalCount
        ];
        return $this->responseData($data);
    }

    /**
     * [GET]
     * [PRIVATE]
     * Fetches the number of followers based on the given user
     * 
     * @return int - number of users
     */
    public function countFollowers()
    {
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        $userId = $this->Users->fetchByUsername($username, 'Users.id')->id;
        $returnData = [
            'count' => $this->Users->Followers->countFollowers($userId)
        ];
        return $this->responseData($returnData);
    }

    /**
     * [GET]
     * [PRIVATE]
     * Fetches the number of users being followed by the given user
     * 
     * @return int - number of users
     */
    public function countFollowing()
    {
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        $userId = $this->Users->fetchByUsername($username, 'Users.id')->id;
        $returnData = [
            'count' => $this->Users->Followers->countFollowing($userId)
        ];
        return $this->responseData($returnData);
    }

    /**
     * [GET]
     * [PRIVATE]
     * Fetches the number of users being followed by the given user
     * and the number of followers of the user
     * 
     * @return array - list of users
     */
    public function countFollow()
    {
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        $userId = $this->Users->fetchByUsername($username, ['Users.id'])->id;
        $returnData = [
            'followerCount' => $this->Users->Followers->countFollowers($userId),
            'followingCount' => $this->Users->Followers->countFollowing($userId)
        ];
        return $this->responseData($returnData);
    }

    /**
     * [POST]
     * [PRIVATE]
     * Follow a user
     * 
     * @return object
     */
    public function follow()
    {
        $this->request->allowMethod('post');
        $userId = $this->request->getParam('id');
        $follower = $this->Users->Followers->toggleFollowUser(
            (int) $userId,
            (int) $this->Auth->user('id')
        );

        if ($follower instanceof Follower) {
            $this->loadComponent('UserHandler');
            $this->UserHandler->notifyAfterFollow($follower);
        }

        $returnData = [
            'followerCount' => $this->Users->Followers->countFollowers($userId),
            'followingCount' => $this->Users->Followers->countFollowing($userId)
        ];
        return $this->responseCreated($returnData);
    }

    /**
     * [GET]
     * [PRIVATE]
     * Checks if username passed is being followed by the current user logged in
     * 
     * @return bool
     */
    public function isFollowing()
    {
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        $userId = $this->Users->fetchByUsername($username, 'Users.id')->id;
        $isFollowing = $this->Users->Followers->isFollowing(
            (int) $this->Auth->user('id'),
            (int) $userId
        );
        return $this->responseData($isFollowing ? 1 : 0);
    }
}
