<?php
namespace App\Controller\Api\User;

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
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        return $this->responseData([
            'count' => $this->Users->fetchFollowers($username)
        ]);
    }

    public function fetchFollowing()
    {

    }

    public function recommended()
    {
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        $userId = $this->Users->fetchByUsername($username, 'Users.id')->id;
        return $this->responseData([
            'users' => $this->Users->fetchRecommendedUsers($userId)
        ]);
    }

    public function countFollowers()
    {
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        $userId = $this->Users->fetchByUsername($username, 'Users.id')->id;
        return $this->responseData([
            'count' => $this->Users->Followers->countFollowers($userId)
        ]);
    }

    public function countFollowing()
    {
        $this->request->allowMethod('get');
        $username = $this->request->getParam('username');
        $userId = $this->Users->fetchByUsername($username, 'Users.id')->id;
        return $this->responseData([
            'count' => $this->Users->Followers->countFollowing($userId)
        ]);
    }

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
