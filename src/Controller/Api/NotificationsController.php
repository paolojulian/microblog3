<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\ORM\TableRegistry;
use Cake\Http\Exception\NotFoundException;

/**
 * Api/Search Controller
 */
class NotificationsController extends AppController
{
    public $paginate = [
        'limit' => 5,
        'order' => [
            'Notifications.created' => 'desc'
        ]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }

    public function isAuthorized()
    {
        if ( ! in_array($this->request->getParam('action'), ['readOne'])) {
            return true;
        }

        if ( ! parent::isOwnedBy($this->Notifications, $this->Auth->user('id'))) {
            return false;
        }

        return parent::isAuthorized();
    }

    /**
     * [GET]
     * [PRIVATE]
     * 
     * Fetch Unread Notifications
     * 
     * @return array
     */
    public function fetchUnread()
    {
        $this->request->allowMethod('get');
        try {
            $query = $this->Notifications->fetchUnreadNotifications(
                $this->Auth->user('id')
            );
            $result = $this->paginate($query);
        } catch (NotFoundException $e) {
            $result = [];
        }
        $this->responseData($result);
    }

    /**
     * [GET]
     * [PRIVATE]
     * 
     * Fetch Read Notifications of current user logged in
     * 
     * @return array
     */
    public function fetchRead()
    {
        $this->request->allowMethod('get');
        try {
            $query = $this->Notifications->fetchReadNotifications(
                $this->Auth->user('id')
            );
            $result = $this->paginate($query);
        } catch (NotFoundException $e) {
            $result = [];
        }
        $this->responseData($result);
    }

    /**
     * [GET]
     * [PRIVATE]
     * 
     * Counts the Unread Notifications of current user logged in
     * 
     * @return array
     */
    public function countUnread()
    {
        $this->request->allowMethod('get');
        try {
            $totalCount = $this->Notifications->countUnreadNotifications(
                $this->Auth->user('id')
            );
        } catch (Exception $e) {
            $totalCount = 0;
        }
        $this->responseData($totalCount);
    }

    /**
     * [POST]
     * [PRIVATE]
     * 
     * Reads all notification of the current user
     * 
     * @return status - 200
     */
    public function readAll()
    {
        $this->request->allowMethod('post');
        $this->Notifications->readAll((int) $this->Auth->user('id'));
        $this->responseOk();
    }

    /**
     * [POST]
     * [PRIVATE]
     * 
     * Reads all notification of the current user
     * 
     * @return status - 200
     */
    public function readOne()
    {
        $this->request->allowMethod('post');
        $notificationId = (int) $this->request->getParam('id');
        $this->Notifications->read($notificationId);
        $this->responseOk();
    }
}
