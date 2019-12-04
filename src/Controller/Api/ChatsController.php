<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Http\Exception\NotFoundException;

/**
 * Api/Search Controller
 */
class ChatsController extends AppController
{
    public $paginate = [
        'limit' => 10,
        'order' => [
            'Chats.created' => 'desc'
        ]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    /**
     * [GET]
     * [PRIVATE]
     *
     * Fetches chat buddies with ther User Entity
     *
     * @return array - App\Model\Entity\User
     */
    public function index()
    {
        $this->request->allowMethod('get');
        $result = $this->Chats->fetchUsersToMessage($this->Auth->user('id'));
        return $this->responseData($result);
    }

    /**
     * [GET]
     * [PRIVATE]
     *
     * Fetches all messages with the given user
     *
     * @return object -
     *  App\Model\Entity\Chat
     *  App\Model\Entity\User
     */
    public function view()
    {
        $this->request->allowMethod('get');
        $receiverId = $this->request->getParam('id');
        $messages = $this->paginate($this->Chats->fetchMessages(
            $receiverId,
            $this->Auth->user('id')
        ));
        $user = $this->Chats->Users->get($receiverId);
        return $this->responseData([
            'messages' => $messages,
            'userInfo' => $user
        ]);
    }

    /**
     * [POST]
     * [PRIVATE]
     *
     * Sends a message to the given user
     *
     * @return object - App\Model\Entity\Chat
     */
    public function add()
    {
        $this->request->allowMethod('post');
        $id = $this->request->getParam('id');
        $requestData = $this->request->getData();
        $requestData['user_id'] = $this->Auth->user('id');
        $chat = $this->Chats->addMessage($requestData);
        return $this->responseData($chat);
    }
}
