<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Http\Exception\NotFoundException;

/**
 * Followers Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\FollowingsTable&\Cake\ORM\Association\BelongsTo $Followings
 *
 * @method \App\Model\Entity\Follower get($primaryKey, $options = [])
 * @method \App\Model\Entity\Follower newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Follower[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Follower|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Follower saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Follower patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Follower[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Follower findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class FollowersTable extends Table
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

        $this->setTable('followers');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('Followings', [
            'className' => 'Users',
            'propertyName' => 'user',
            'foreignKey' => 'following_id',
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
            ->dateTime('deleted')
            ->allowEmptyDateTime('deleted');

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
        $rules->add($rules->existsIn(['following_id'], 'Users'));

        return $rules;
    }

    /**
     * Fetches the followers of the user given
     * 
     * @param int $userId - users.id
     * @param int $page - page number
     * @param int $perPage - max data per page
     * 
     * @return array of users
     */
    public function fetchFollowers(int $userId, int $page = 1, int $perPage = 10)
    {
        return $this->find()
            ->select([
                'Followers.user_id',
                'isFollowing.id'
            ])
            ->where(['Followers.following_id' => $userId])
            ->contain([
                'Users' => function ($q) {
                    return $q->select(['id', 'username', 'avatar_url', 'first_name', 'last_name']);
                }
            ])
            ->join([
                'isFollowing' => [
                    'table' => 'Followers',
                    'type' => 'LEFT',
                    'conditions' => [
                        'isFollowing.following_id = Followers.user_id',
                        "isFollowing.user_id = $userId"
                    ]
                ]
            ])
            ->limit($perPage)
            ->page($page)
            ->toList();
    }

    /**
     * Fetches the users being followed by the given user
     * 
     * @param int $userId - users.id
     * @param int $userId - users.id
     * @param int $page - page number
     * @param int $perPage - max data per page
     * 
     * @return array of users
     */
    public function fetchFollowing(int $userId, int $page = 1, int $perPage = 10)
    {
        return $this->find()
            ->select(['Followers.following_id'])
            ->where(['user_id' => $userId])
            ->contain([
                'Followings' => function ($q) {
                    return $q->select(['id', 'username', 'avatar_url', 'first_name', 'last_name']);
                }
            ])
            ->limit($perPage)
            ->page($page)
            ->toList();
    }

    /**
     * Counts the followers of the given user
     * 
     * @param int $userId - users.id
     * @return int
     */
    public function countFollowers($userId)
    {
        return $this->find()
            ->where(['following_id' => $userId])
            ->count();
    }

    /**
     * Counts the users the is being followed by the given user
     * 
     * @param int $userId - users.id
     * @return int
     */
    public function countFollowing($userId)
    {
        return $this->find()
            ->where(['user_id' => $userId])
            ->count();
    }

    /**
     * Toggles the follow a user
     * 
     * @param int $followingId - users.id - The user to be followed
     * @param int $userId - users.id - The user to follow
     * 
     * @return void
     */
    public function toggleFollowUser(int $followingId, int $userId)
    {
        if ( ! $this->Users->exists(['id' => $followingId])) {
            throw new NotFoundException();
        }

        $followEntity = $this->find()
            ->where([
                'following_id' => $followingId,
                'user_id' => $userId
            ])
            ->first();

        if ($followEntity) {
            // Delete follow entity
            if ( ! $this->delete($followEntity)) {
                throw new InternalErrorException();
            }
            return true;
        }

        $followEntity = $this->newEntity();
        $followEntity->user_id = $userId;
        $followEntity->following_id = $followingId;
        if ( ! $this->save($followEntity)) {
            throw new InternalErrorException();
        }

        return true;
    }

    /**
     * Checks if user follows certain user
     * 
     * @param int $userId - users.id
     * @param int $followedUserId - users.id
     * @return int
     */
    public function isFollowing(int $userId, int $followedUserId)
    {
        return $this->exists([
            'user_id' => $userId,
            'following_id' => $followedUserId
        ]);
    }
}
