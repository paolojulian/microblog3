<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use App\Lib\Interfaces\NotificationInterface;
use App\Lib\DTO\NotificationDTO;
/**
 * MailHandler component
 */
class NotificationHandlerComponent extends Component implements NotificationInterface
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    const LIKED = 'liked';
    const COMMENTED = 'commented';
    const SHARED = 'shared';
    const FOLLOWED = 'followed';

    /**
     * @Override
     */
    public function notifyUser(NotificationDTO $notificationDTO)
    {
        $this->Notifications = TableRegistry::get('Notifications');
        $this->Notifications->addNotification($notificationDTO->getData());
    }
}
