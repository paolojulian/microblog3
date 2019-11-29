<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Notification Entity
 *
 * @property int $id
 * @property string|null $message
 * @property int $user_id
 * @property int $receiver_id
 * @property int|null $post_id
 * @property string|null $link
 * @property \Cake\I18n\FrozenTime|null $is_read
 * @property string|null $type
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Receiver $receiver
 * @property \App\Model\Entity\Post $post
 */
class Notification extends Entity
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
        'message' => true,
        'user_id' => true,
        'receiver_id' => true,
        'post_id' => true,
        'link' => true,
        'is_read' => true,
        'type' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'receiver' => true,
        'post' => true
    ];
}
