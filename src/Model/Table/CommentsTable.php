<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;

/**
 * Comments Model
 *
 * @property \App\Model\Table\PostsTable&\Cake\ORM\Association\BelongsTo $Posts
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 *
 * @method \App\Model\Entity\Comment get($primaryKey, $options = [])
 * @method \App\Model\Entity\Comment newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Comment[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Comment|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Comment saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Comment patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Comment[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Comment findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CommentsTable extends Table
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
        $this->addBehavior('Trimmer');

        $this->setTable('comments');
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
            ->scalar('body')
            ->maxLength('body', 140, __('Maximum of 140 characters only'))
            ->requirePresence('body', true, __('Please enter your message'))
            ->notEmptyString('body', __('Please enter your message'));

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
     * Adds a comment to a post
     * 
     * @param int $postId - posts.id
     * @param int $userId - users.id
     * @param array $data - Data to be inserted
     * 
     * @return App\Model\Entity\Comment
     */
    public function addCommentToPost(int $postId, int $userId, array $data)
    {
        $comment = $this->newEntity($data);
        $comment->post_id = $postId;
        $comment->user_id = $userId;

        if ( ! $this->Posts->exists(['id' => $postId])) {
            throw new NotFoundException();
        }

        if ($comment->hasErrors()) {
            return $comment;
        }

        if ( ! $this->save($comment)) {
            throw new InternalErrorException();
        }

        return $comment;
    }

    /**
     * Fetches comments of the given post
     * 
     * @param int $postId - posts.id
     * @param int $page - page
     * @param int $perPage
     * 
     * @return \Cake\ORM\Query
     */
    public function fetchPerPost(int $postId, int $page, int $perPage = 10)
    {
        return $this->find()
            ->select(['Comments.id', 'Comments.body', 'Comments.created'])
            ->contain(['Users' => function ($q) {
                return $q->select(['username', 'avatar_url', 'id']);
            }])
            ->where(['post_id' => $postId])
            ->page(1)
            ->limit(10);
    }
        
    /**
     * Counts the number of comments in a post
     * 
     * @param int $postId - posts.id
     * @return int
     */
    public function countPerPost(int $postId)
    {
        return $this->find()
            ->where(['post_id' => $postId])
            ->count();
    }
}
