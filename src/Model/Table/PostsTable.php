<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;

/**
 * Posts Model
 *
 * @property \App\Model\Table\RetweetPostsTable&\Cake\ORM\Association\BelongsTo $RetweetPosts
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\CommentsTable&\Cake\ORM\Association\HasMany $Comments
 * @property \App\Model\Table\LikesTable&\Cake\ORM\Association\HasMany $Likes
 * @property \App\Model\Table\NotificationsTable&\Cake\ORM\Association\HasMany $Notifications
 *
 * @method \App\Model\Entity\Post get($primaryKey, $options = [])
 * @method \App\Model\Entity\Post newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Post[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Post|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Post saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Post patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Post[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Post findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class PostsTable extends Table
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

        $this->setTable('posts');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('RetweetPosts', [
            'foreignKey' => 'retweet_post_id'
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Comments', [
            'foreignKey' => 'post_id'
        ]);
        $this->hasMany('Likes', [
            'foreignKey' => 'post_id'
        ]);
        $this->hasMany('Notifications', [
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
            ->scalar('title')
            ->maxLength('title', 30, __('Maximum of 30 characters is allowed'))
            ->allowEmptyString('title');

        $validator
            ->scalar('body')
            ->requirePresence('body', true, __('Please enter your message'))
            ->maxLength('body', 140, __('Maximum of 140 characters is allowed'))
            ->notEmptyString('body', __('Please enter your message'));

        $validator
            ->scalar('user_id')
            ->notEmptyString('user_id');

        $validator
            ->scalar('img_path')
            ->maxLength('img_path', 255)
            ->allowEmptyString('img_path');

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

        return $rules;
    }

    /**
     * Fetch a single post
     * if it is a shared post,
     * include the original post
     * 
     * @param int $postId - posts.id - The post to fetch
     * @param bool $includeLikesAndComments - check if will include likes and comments
     * @return object - Post Entity/s
     */
    public function fetchPost(int $postId, bool $includeLikesAndComments = true)
    {
        $post = $this->find()
            ->where(['Posts.id' => $postId])
            ->contain([
                'Users' => function($q) {
                    return $q->select(['Users.id', 'Users.username', 'Users.avatar_url']);
                },
                // 'Likes' => function($q) {
                //     $q->select([
                //             'Likes.user_id',
                //             'Likes.post_id',
                //             'total' => $q->func()->count('Likes.user_id')
                //         ])
                //         ->group(['Likes.post_id']);
                //     return $q;
                // },
            ])
            ->first();
        
        if ($includeLikesAndComments) {
            $post->likes = $this->Likes->fetchLikersOfPost($postId);
            $post->comments = $this->Comments->countPerPost($postId);
        }

        if (!!$post->retweet_post_id) {
            $originalPost = $this->fetchPost($post->retweet_post_id, false);
            return [
                'post' => $originalPost['post'],
                'sharedPost' => $post,
                'isShared' => true
            ];
        }
        return [
            'post' => $post,
            'isShared' => false
        ];
    }

    /**
     * Fetches all posts that will be displayed in the landing page
     * 
     * Fetches posts U shared_posts of owned OR followed users
     */
    public function fetchPostsForLanding($userId, $pageNo = 1, $perPage = 5)
    {
        $this->connection = ConnectionManager::get('default');
        $offset = ($pageNo - 1) * $perPage;
        $results = $this->connection->execute(
            'CALL fetchPostsToDisplay(?, ?, ?)', 
            [$userId, $perPage, $offset]
        )->fetchAll('assoc');

        $this->populateWithLikesAndComments($results);
        return $results;
    }

    /**
     * Fetches all posts that will be displayed in the landing page
     * 
     * Fetches posts U shared_posts of owned OR followed users
     */
    public function fetchPostsForUser($userId, $pageNo = 1, $perPage = 5)
    {
        $this->connection = ConnectionManager::get('default');
        $offset = ($pageNo - 1) * $perPage;
        $results = $this->connection->execute(
            'CALL fetchPostsOfUser(?, ?, ?)', 
            [$userId, $perPage, $offset]
        )->fetchAll('assoc');

        $this->populateWithLikesAndComments($results);
        return $results;
    }

    /**
     * Adds the likes and comments of given posts
     * 
     * @param array &$data - array of posts
     * @return void
     */
    private function populateWithLikesAndComments(&$data)
    {
        foreach ($data as $key => $item) {
            $data[$key]['likes'] = $this->Likes->fetchLikersOfPost($item['id']);
            $data[$key]['comments'] = $this->Comments->countPerPost($item['id']);
        }
    }

    /**
     * Adds a post to the database
     * 
     * @param array $data - Post Entity
     * @return array - status and Post Enitity
     */
    public function addPost(array $data)
    {
        $post = $this->newEntity($data);
        $errors = $post->errors();
        if ($errors) {
            return [
                'status' => false,
                'errors' => $errors
            ];
        }
        if ( ! $this->save($post)) {
            throw new InternalErrorException();
        }
        return [
            'status' => true,
            'entity' => $post
        ];
    }

    /**
     * Updates a post
     * 
     * @param integer $postId - posts.id - Post to be updated
     * @param array $data - Post Entity
     * @return array - status and Post Enitity
     */
    public function updatePost(int $postId, array $data)
    {
        $post = $this->get($postId);
        $this->patchEntity($post, $data);
        if ($post->hasErrors()) {
            return $post;
        }
        if ( ! $this->save($post)) {
            throw new InternalErrorException();
        }
        return $post;
    }

    /**
     * Deletes a post from the database
     * 
     * @param integer $postId - posts.id - Post to be deleted
     */
    public function deletePost(int $postId)
    {
        $post = $this->get($postId);
        if ( ! $this->delete($post)) {
            throw new InternalErrorException();
        }
        return true;
    }

    public function beforeSave($event, $entity, $options)
    {
        $entity->set('title', trim($entity->title));
        $entity->set('body', trim($entity->body));
    }

    /**
     * Checks if Post is owned by the user
     */
    public function isOwnedBy($postId, $userId)
    {
        return $this->exists(['id' => $postId, 'user_id' => $userId]);
    }
}