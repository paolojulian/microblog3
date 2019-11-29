<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\FrozenTime;
use Cake\Http\Exception\InternalErrorException;

/**
 * Notifications Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\ReceiversTable&\Cake\ORM\Association\BelongsTo $Receivers
 * @property \App\Model\Table\PostsTable&\Cake\ORM\Association\BelongsTo $Posts
 *
 * @method \App\Model\Entity\Notification get($primaryKey, $options = [])
 * @method \App\Model\Entity\Notification newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Notification[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Notification|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Notification saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Notification patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Notification[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Notification findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class NotificationsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('notifications');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Posts', [
            'foreignKey' => 'post_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('message')
            ->allowEmptyString('message');

        $validator
            ->scalar('link')
            ->allowEmptyString('link');

        $validator
            ->dateTime('is_read')
            ->allowEmptyDateTime('is_read');

        $validator
            ->scalar('type')
            ->allowEmptyString('type');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['post_id'], 'Posts'));

        return $rules;
    }

    /**
     * Fetches the unread notifications of the user
     * 
     * @param int $userId - notifications.receiver_id
     * @return \Cake\ORM\Query
     */
    public function fetchUnreadNotifications(int $userId)
    {
        return $this->find()
            ->select([
                'Notifications.id',
                'Notifications.message',
                'Notifications.user_id',
                'Notifications.post_id',
                'Notifications.link',
                'Notifications.type',
                'Users.username',
                'Users.avatar_url',
            ])
            ->contain(['Users'])
            ->where([
                'receiver_id' => $userId,
                'is_read IS NULL'
            ]);
    }

    /**
     * Fetches the read notifications of the user
     * 
     * @param int $userId - notifications.receiver_id
     * @return \Cake\ORM\Query
     */
    public function fetchReadNotifications(int $userId)
    {
        return $this->find()
            ->select([
                'Notifications.id',
                'Notifications.message',
                'Notifications.user_id',
                'Notifications.post_id',
                'Notifications.link',
                'Notifications.type',
                'Users.username',
                'Users.avatar_url',
            ])
            ->contain(['Users'])
            ->where([
                'receiver_id' => $userId,
                'is_read IS NOT NULL'
            ]);
    }

    /**
     * Fetches the total count of unread notifications
     * of the given user
     * 
     * @param int $userId - notifications.receiver_id
     * 
     * @return int - Total count
     */
    public function countUnreadNotifications(int $userId)
    {
        return $this->find()
            ->where([
                'receiver_id' => $userId,
                'is_read IS NULL'
            ])
            ->count();
    }

    /**
     * Reads a notification
     * 
     * @param int $notificationId - notifications.id
     * 
     * @return void
     */
    public function read(int $notificationId)
    {
        $notification = $this->get($notificationId);
        $notification->is_read = FrozenTime::now();
        if ( ! $this->save($notification)) {
            throw new InternalErrorException();
        }
    }

    /**
     * Reads all notifications of given user
     * 
     * @param int $userId - notifications.receiver_id
     * 
     * @return void
     */
    public function readAll(int $userId)
    {
        $fields = ['is_read' => FrozenTime::now()];
        $conditions = [
            'receiver_id' => $userId,
            'is_read IS NULL'
        ];
        $this->updateAll($fields, $conditions);
    }

    /**
     * Checks if Entity is owned by the user
     */
    public function isOwnedBy($notificationId, $userId)
    {
        return $this->exists(['id' => $notificationId, 'receiver_id' => $userId]);
    }
}
