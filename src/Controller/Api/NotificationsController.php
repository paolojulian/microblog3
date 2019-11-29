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
        'UnreadNotifications' => [
            'scope' => 'unread_notifications',
            'limit' => 5,
            'order' => [
                'Notifications.created' => 'desc'
            ]
        ],
    ];

    /**
     * Fetch Unread Notifications
     * 
     * @return array
     */
    public function unread()
    {
        try {
            $query = $this->Notifications->fetchUnreadNotifications(
                $this->Auth->user('id')
            );
            $result = $this->paginate($query, ['scope' => 'unread_notifications']);
        } catch (Exception $e) {
            $result = [];
        }
        $this->responseData($result);
    }
}
