<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\ORM\TableRegistry;
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
        $query = $this->Chats->fetchUsersToMessage($this->Auth->user('id'));
        $result = $query->disableHydration()->toArray();
        return $this->responseData($result);
    }

    /**
     * [GET]
     * [PRIVATE]
     *
     * Fetches all messages with the given user
     *
     * @return array - App\Model\Entity\Chat
     */
    public function view()
    {
        $this->request->allowMethod('get');
        $messages = $this->paginate($this->Chats->fetchMessages(
            $this->Auth->user('id'),
            $this->request->getParam('id')
        ));
        return $this->responseData($messages);
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
        $requestData['receiver_id'] = $id;
        $chat = $this->Chats->addMessage($requestData);
        return $this->responseData($chat);
    }
}
