<?php
namespace App\Lib\DTO;

use App\Model\Entity\Like;
use App\Model\Entity\Comment;
use App\Model\Entity\Post;
use App\Model\Entity\Follower;

/**
 * TODO: change postId to link
 */
class NotificationDTO
{
    private $type; // Type of notification
    private $userId; // User who initiated the action
    private $receiverId; // User to be notified
    private $postId; // The Post

    const LIKED = 'liked';
    const COMMENTED = 'commented';
    const SHARED = 'shared';
    const FOLLOWED = 'followed';

    public function getData()
    {
        return [
            'type' => $this->type,
            'receiver_id' => $this->receiverId,
            'user_id' => $this->userId,
            'post_id' => $this->postId,
        ];
    }

    public function setLiked(int $userId, int $receiverId, int $postId)
    {
        $this->type = self::LIKED;
        $this->userId = $userId;
        $this->receiverId = $receiverId;
        $this->postId = $postId;
    }

    public function setCommented(int $userId, int $receiverId, int $postId)
    {
        $this->type = self::COMMENTED;
        $this->userId = $userId;
        $this->receiverId = $receiverId;
        $this->postId = $postId;
    }

    public function setShared(int $userId, int $receiverId, int $postId)
    {
        $this->type = self::SHARED;
        $this->userId = $userId;
        $this->receiverId = $receiverId;
        $this->postId = $postId;
    }

    public function setFollowed(int $userId, int $receiverId)
    {
        $this->type = self::FOLLOWED;
        $this->userId = $userId;
        $this->receiverId = $receiverId;
        $this->postId = null;
    }
}