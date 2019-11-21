<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property \Cake\I18n\FrozenDate $birthdate
 * @property string|null $password
 * @property string|null $sex
 * @property string $role
 * @property string|null $avatar_url
 * @property bool $is_activated
 * @property string|null $activation_key
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $deleted
 *
 * @property \App\Model\Entity\Comment[] $comments
 * @property \App\Model\Entity\Follower[] $followers
 * @property \App\Model\Entity\Like[] $likes
 * @property \App\Model\Entity\Notification[] $notifications
 * @property \App\Model\Entity\Post[] $posts
 */
class User extends Entity
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
        'username' => true,
        'first_name' => true,
        'last_name' => true,
        'email' => true,
        'birthdate' => true,
        'password' => true,
        'sex' => true,
        'role' => true,
        'avatar_url' => true,
        'is_activated' => true,
        'activation_key' => true,
        'created' => true,
        'modified' => true,
        'deleted' => false,
        'comments' => true,
        'followers' => true,
        'likes' => true,
        'notifications' => true,
        'posts' => true
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];
    
    protected function _setPassword($password)
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher)->hash($password);
        }
    }

}
