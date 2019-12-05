<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Exception\NotFoundException;
use SoftDelete\Model\Table\SoftDeleteTrait;

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
    use SoftDeleteTrait;
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
        $this->addBehavior('Trimmer');

        $this->belongsTo('RetweetPosts', [
            'foreignKey' => 'retweet_post_id',
            'className' => 'Posts',
            'propertyName' => 'original_post',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT'
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

    public function validationShare(Validator $validator) {
        $validator
            ->scalar('body')
            ->maxLength('body', 140, __('Maximum of 140 characters is allowed'))
            ->allowEmptyString('body', true);
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
        // $followedUsersQuery = $this->Users->Followers->fetchFollowedByUser($userId);

        // $result = $this->find()
        //     ->where([
        //         'Posts.retweet_post_id IS NOT NULL',
        //         'OR' => [
        //             'Posts.user_id IN' => $followedUsersQuery,
        //             'Posts.user_id' => $userId
        //         ]
        //     ])
        //     ->contain([
        //         'RetweetPosts',
        //         'Users' => function ($q) {
        //             return $q->select(['id', 'username', 'first_name', 'last_name', 'avatar_url']);
        //         }
        //     ])
        //     ->group(['Posts.retweet_post_id']);

        // $result2 = $this->find()
        //     ->where([
        //         'OR' => [
        //             'Posts.user_id IN' => $followedUsersQuery,
        //             'Posts.user_id' => $userId
        //         ]
        //     ])
        //     ->contain([
        //         'RetweetPosts',
        //         'Users' => function ($q) {
        //             return $q->select(['id', 'username', 'first_name', 'last_name', 'avatar_url']);
        //         }
        //     ]);
        
        // $resultQuery = $result->union($result2);
        // $test = $this->find()
        //         ->from([$this->alias() => $resultQuery])
        //         ->order(['posts.created' => 'desc'])
        //         ->disableHydration();
        
        // return $test->toArray();
        // foreach ($result as $key => $post) {
        //     if ($post['retweet_post_id']) {
        //         $result[$key]['users_who_shared'] = $this
        //             ->fetchUsersWhoSharedPostQuery(
        //                 $post,
        //                 $followedUsersQuery
        //             )
        //             ->disableHydration()
        //             ->toArray();
        //     }
        // }
        // return $result;

        $this->connection = ConnectionManager::get('default');
        $offset = ($pageNo - 1) * $perPage;
        $results = $this->connection->execute(
            'CALL fetchPostsToDisplay(?, ?, ?)', 
            [$userId, $perPage, $offset]
        )->fetchAll('assoc');

        $this->populateWithLikesAndComments($results);
        $followedUsersQuery = $this->Users->Followers->fetchFollowedByUser($userId);
        $this->test($results, $followedUsersQuery);
        return $results;
    }

    public function test(array &$results, $query)
    {
        foreach ($results as $key => $post) {
            if ($post['retweet_post_id']) {
                $results[$key]['users_who_shared'] = $this
                    ->fetchUsersWhoSharedPostQuery(
                        $post,
                        $query
                    )
                    ->disableHydration()
                    ->toArray();
            }
        }
    }

    /**
     * Fetch Followed Users who shared the same post given
     * 
     * @param array $post - The shared post
     * @param object App\ORM\Query - Followed users
     * 
     * @return object App\ORM\Query
     */
    public function fetchUsersWhoSharedPostQuery(array $post, object $query)
    {
        return $this->find()
            ->contain(['Users' => function ($q) {
                return $q->select(['id', 'username', 'first_name', 'last_name', 'avatar_url']);
            }])
            ->where([
                'Posts.id <>' => $post['id'],
                'Posts.retweet_post_id' => $post['retweet_post_id'],
                'Posts.user_id IN' => $query,
            ])
            ->order(['Posts.created' => 'DESC'])
            ->limit(3);
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

    /**
     * Shares a Post
     * 
     * @param int $postId - the post to be shared
     * @param int $userId - the user who shared the post
     * 
     * @return array - status and Post Enitity
     */
    public function sharePost(int $postId, int $userId, array $data)
    {
        if ( ! $this->exists(['id' => $postId])) {
            throw new NotFoundException();
        }
        $post = $this->newEntity($data, ['validate' => 'Share']);
        $post->retweet_post_id = $postId;
        $post->user_id = $userId;

        if ($post->hasErrors()) {
            return $post;
        }

        if ( ! $this->save($post)) {
            throw new InternalErrorException();
        }

        return $post;
    }

    /**
     * Searches a post with given text
     * wont search if is a shared post
     * 
     * @param string $text - the string to match the users
     * @param int $page
     * @param int $perPage - max number of data per page
     * 
     * @return array
     */
    public function searchPosts(string $text, int $page = 1, int $perPage = 5)
    {
        $conditions = [
            'OR' => [
                'title LIKE' => "%$text%",
                'body LIKE' => "%$text%",
            ],
            "retweet_post_id IS NULL"
        ];

        $list = $this->find()
            ->select([
                'id',
                'title',
                'body',
                'user_id',
                'img_path',
                'created'
            ])
            ->contain(['Users' => function($q) {
                return $q->select(['username', 'avatar_url']);
            }])
            ->where($conditions)
            ->order(['Posts.created' => 'DESC'])
            ->limit($perPage)
            ->page($page)
            ->disableHydration()
            ->toArray();

        $totalCount = $this->find()
            ->where($conditions)
            ->contain(['Users'])
            ->count();

        return [
            'list' => $list,
            'totalCount' => $totalCount
        ];
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