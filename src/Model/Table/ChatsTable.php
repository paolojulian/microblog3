<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Datasource\ConnectionManager;
use Cake\Validation\Validator;
use Cake\Network\Exception\InternalErrorException;

/**
 * Chats Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\ReceiversTable&\Cake\ORM\Association\BelongsTo $Receivers
 *
 * @method \App\Model\Entity\Chat get($primaryKey, $options = [])
 * @method \App\Model\Entity\Chat newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Chat[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Chat|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Chat saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Chat patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Chat[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Chat findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ChatsTable extends Table
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

        $this->setTable('chats');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Receivers', [
            'className' => 'Users',
            'propertyName' => 'receiver',
            'foreignKey' => 'receiver_id',
            'joinType' => 'INNER'
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
            ->maxLength('message', 255, __('Up to 255 characters only'))
            ->requirePresence('message', 'create', __('Message is required'))
            ->notEmptyString('message', __('Message is required'));

        $validator
            ->dateTime('is_read')
            ->allowEmptyDateTime('is_read');

        $validator
            ->dateTime('deleted')
            ->allowEmptyDateTime('deleted');

        return $validator;
    }

    /**
     * Adds a message entity
     *
     * @param array $data - Data to be saved
     *
     * @return object App\Model\Entity\Chat
     */
    public function addMessage(array $data)
    {
        $chat = $this->newEntity($data);
        if ($chat->hasErrors()) {
            return $chat;
        }

        if ( ! $this->save($chat)) {
            throw new InternalErrorException();
        }

        return $chat;
    }

    /**
     * Fetches messages of the given user
     *
     * @param int $userId - the user who sent the message
     * @param int $receiverId - the user who received the message
     *
     * @return object Cake\ORM\Query
     */
    public function fetchMessages(int $userId, int $receiverId) {
        return $this->find()
            ->where([
                'OR' => [
                    'user_id' => $userId,
                    'receiver_id' => $userId
                ]
            ]);
    }

    /**
     * Fetches Users who has message with current user
     *
     * @param int $userId - current user logged in
     * @param int $page - page number
     * @param int $perPage - number of data per page
     *
     * @return object Cake\ORM\Query
     */
    public function fetchUsersToMessage(int $userId, int $page = 1, int $perPage = 20) {
        $this->connection = ConnectionManager::get('default');
        $offset = ($page - 1) * $perPage;
        $results = $this->connection->execute(
            'CALL fetchUsersToChat(?, ?, ?)', 
            [$userId, $perPage, $offset]
        )->fetchAll('assoc');
        return $results;
    }
}
