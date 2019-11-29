<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\ORM\TableRegistry;

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
        try {
            $query = $this->Notifications->fetchUnreadNotifications(
                $this->Auth->user('id')
            );
            $result = $this->paginate($query);
        } catch (Exception $e) {
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
        try {
            $query = $this->Notifications->fetchReadNotifications(
                $this->Auth->user('id')
            );
            $result = $this->paginate($query);
        } catch (Exception $e) {
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
        try {
            $totalCount = $this->Notifications->countUnreadNotifications(
                $this->Auth->user('id')
            );
        } catch (Exception $e) {
            $totalCount = 0;
        }
        $this->responseData($totalCount);
    }
}
