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
    public function fetchFollowers()
    {
        // TODO
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        return $this->responseData([
            'count' => $this->Users->fetchFollowers($username)
        ]);
    }

    public function fetchFollowing()
    {
        // TODO
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
        $userId = $this->Users->fetchByUsername($username, 'Users.id')->id;
        return $this->responseData([
            'followerCount' => $this->Users->Followers->countFollowers($userId),
            'followingCount' => $this->Users->Followers->countFollowing($userId)
        ]);
    }
}
