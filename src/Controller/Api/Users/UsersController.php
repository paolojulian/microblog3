<?php
namespace App\Controller\Api\Users;

use App\Controller\Api\AppController;

/**
 * Api/User/Users Controller
 *
 *
 * @method \App\Model\Entity\Api/User/User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    /**
     * Fetches the profile
     * of the given username
     * 
     * @param string username - users.username
     * @return object User Entity
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
     * Fetches the mutual following with the given user
     * 
     * @param string username - users.username
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

    public function fetchFollowers()
    {
        $this->request->allowMethod('get');
        $id = $this->request->getParam('id');
        $page = $this->request->getQuery('page', 1);
        return $this->responseData($this->Users->Followers->fetchFollowers($id, $page));
    }

    public function fetchFollowing()
    {
        $this->request->allowMethod('get');
        $id = $this->request->getParam('id');
        $page = $this->request->getQuery('page', 1);
        return $this->responseData($this->Users->Followers->fetchFollowing($id, $page));
    }

    /**
     * [GET]
     * [PRIVATE]
     * Fetches recommended users of the current user logged in
     * 
     * @param string - username
     * @return array - list of users
     */
    public function recommended()
    {
        $this->request->allowMethod('get');
        return $this->responseData(
            $this->Users->fetchRecommendedUsers($this->Auth->user('id'))
        );
    }

    /**
     * [GET]
     * [PRIVATE]
     * Fetches the number of followers based on the given user
     * 
     * @param string - username
     * @return int - number of users
     */
    public function countFollowers()
    {
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        $userId = $this->Users->fetchByUsername($username, 'Users.id')->id;
        return $this->responseData([
            'count' => $this->Users->Followers->countFollowers($userId)
        ]);
    }

    /**
     * [GET]
     * [PRIVATE]
     * Fetches the number of users being followed by the given user
     * 
     * @param string - username
     * @return int - number of users
     */
    public function countFollowing()
    {
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        $userId = $this->Users->fetchByUsername($username, 'Users.id')->id;
        return $this->responseData([
            'count' => $this->Users->Followers->countFollowing($userId)
        ]);
    }

    /**
     * [GET]
     * [PRIVATE]
     * Fetches the number of users being followed by the given user
     * and the number of followers of the user
     * 
     * @param string - username
     * @return array - list of users
     */
    public function countFollow()
    {
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        $userId = $this->Users->fetchByUsername($username, ['Users.id'])->id;
        return $this->responseData([
            'followerCount' => $this->Users->Followers->countFollowers($userId),
            'followingCount' => $this->Users->Followers->countFollowing($userId)
        ]);
    }

    /**
     * [POST]
     * [PRIVATE]
     * Follow a user
     * 
     * @param int - users.id
     * @return int - the updated count of followers of the given user
     * @return int - the updated count of users the current user is following
     */
    public function follow()
    {
        $this->request->allowMethod('post');
        $userId = $this->request->getParam('id');
        $this->Users->Followers->toggleFollowUser(
            (int) $userId,
            (int) $this->Auth->user('id')
        );
        return $this->responseCreated([
            'followerCount' => $this->Users->Followers->countFollowers($userId),
            'followingCount' => $this->Users->Followers->countFollowers($this->Auth->user('id'))
        ]);
    }

    /**
     * [GET]
     * [PRIVATE]
     * Check if user passed is being followed by the user
     * 
     * @param string - users.username
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
