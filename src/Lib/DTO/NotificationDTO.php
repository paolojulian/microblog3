<?php
namespace App\Lib\DTO;

class NotificationDTO
{
    private $type;
    private $userId;
    private $receiverId;
    private $postId;

    /**
     * TODO: change postId to link
     * 
     * @param string $type - Type of notification
     * @param int $userId - user who initiated the action
     * @param int $receiverId - user to be notified
     * @param int $postId - the post
     */
    function __construct(string $type, int $userId, int $receiverId, int $postId)
    {
        $this->type = $type;
        $this->userId = $userId;
        $this->receiverId = $receiverId;
        $this->postId = $postId;
    }

    public function getData()
    {
        return [
            'type' => $this->type,
            'receiver_id' => $this->receiverId,
            'user_id' => $this->userId,
            'post_id' => $this->postId,
        ];
    }
}