<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Likes Model
 *
 * @property \App\Model\Table\PostsTable&\Cake\ORM\Association\BelongsTo $Posts
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Like get($primaryKey, $options = [])
 * @method \App\Model\Entity\Like newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Like[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Like|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Like saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Like patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Like[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Like findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class LikesTable extends Table
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

        $this->setTable('likes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Posts', [
            'foreignKey' => 'post_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
        $rules->add($rules->existsIn(['post_id'], 'Posts'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    /**
     * Toggles like of a post
     * @param int $userId - users.id - user that liked/unliked the post
     * @param int $postId - posts.id - post that was liked/unliked
     * 
     * @return void
     */
    public function toggleLike(int $userId, int $postId)
    {
        $like = $this->find()
            ->where([
                'post_id' => $postId,
                'user_id' => $userId
            ])
            ->first();
        if ($like) {
            if ( ! $this->delete($like)) {
                throw new InternalErrorException();
            }
            return;
        }

        $like = $this->newEntity();
        $like->user_id = $userId;
        $like->post_id = $postId;
        if ( ! $this->save($like)) {
            throw new InternalErrorException();
        }
        return;
    }

    /**
     * Counts the number of likes of a post
     * 
     * @param int $postId - posts.id
     */
    public function countByPost(int $postId)
    {
        return $this->find()
            ->where([
                'post_id' => $postId
            ])
            ->count();
    }

    /**
     * Fetches all Likers of given post
     * 
     * @param int $postId - posts.id
     * @param int $page - page number
     * @param int $perPage - Max number of data to retrieve
     * 
     * @return array of \App\Model\Entity\Like
     */
    public function fetchByPost(int $postId, int $page = 1, int $perPage = 10)
    {
        return $this->find()
            ->select(['Likes.user_id'])
            ->where(['Likes.post_id' => $postId])
            ->contain(['Users' => function ($q) {
                return $q->select([
                    'Users.username',
                    'Users.avatar_url',
                    'Users.first_name',
                    'Users.last_name'
                ]);
            }])
            ->order(['Likes.created' => 'DESC'])
            ->limit($perPage)
            ->page($page);
    }

    /**
     * Fetches the list of users who like the given post
     * 
     * @param int $postId - posts.id
     * @return array - users_id
     */
    public function fetchLikersOfPost(int $postId)
    {
        $likers = $this->find()
            ->select(['user_id'])
            ->where(['post_id' => $postId])
            ->extract('user_id')
            ->toArray();
        return $likers;
    }
}
