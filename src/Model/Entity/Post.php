<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Post Entity
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $body
 * @property int|null $retweet_post_id
 * @property int $user_id
 * @property string|null $img_path
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $deleted
 *
 * @property \App\Model\Entity\RetweetPost $retweet_post
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Comment[] $comments
 * @property \App\Model\Entity\Like[] $likes
 * @property \App\Model\Entity\Notification[] $notifications
 */
class Post extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'title' => true,
        'body' => true,
        'retweet_post_id' => true,
        'user_id' => true,
        'img_path' => true,
        'created' => true,
        'modified' => true,
        'deleted' => true,
        'retweet_post' => true,
        'user' => true,
        'comments' => true,
        'likes' => true,
        'notifications' => true
    ];
}
