<?php
namespace App\Lib\Interfaces;

use App\Lib\DTO\NotificationDTO;

interface NotificationInterface
{

    /**
     * Notifies The Receiving End of the user
     */
    public function notifyUser(NotificationDTO $notificationDTO);
}