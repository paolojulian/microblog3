<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 *
 * Cache: Routes are cached to improve performance, check the RoutingMiddleware
 * constructor in your `src/Application.php` file to change this behavior.
 *
 */
Router::defaultRouteClass(DashedRoute::class);

Router::prefix('api', function (RouteBuilder $routes) {

    $routes->setExtensions(['json']);

    $routes->prefix('auth', function (RouteBuilder $routes) {
        $routes->connect('/login', ['controller' => 'Auths', 'action' => 'login'])
            ->setMethods(['POST']);
        $routes->connect('/register', ['controller' => 'Auths', 'action' => 'register'])
            ->setMethods(['POST']);
        $routes->connect('/activate/:key', ['controller' => 'Auths', 'action' => 'activate'])
            ->setMethods(['GET']);
        $routes->connect('/me', ['controller' => 'Auths', 'action' => 'me']);
    });

    /************************
     * POSTS
     */
    $routes->prefix('posts', function (RouteBuilder $routes) {
        /** Fetch Posts to display */
        $routes->connect(
            '/',
            ['controller' => 'Posts', 'action' => 'fetchPosts']
        )->setMethods(['GET']);

        /** Fetch Posts of user */
        $routes->connect(
            '/users/:username',
            ['controller' => 'Posts', 'action' => 'fetchPostsOfUser']
        )->setMethods(['GET']);

        /** Add */
        $routes->connect(
            '/',
            ['controller' => 'Posts', 'action' => 'create']
        )->setMethods(['POST']);
        
        /** Update */
        $routes->connect(
            '/update/:id',
            ['controller' => 'Posts', 'action' => 'update']
        )->setPatterns(['id' => '\d+'])
        ->setMethods(['POST']);

        /** Like */
        $routes->connect(
            '/like/:id',
            ['controller' => 'Posts', 'action' => 'like']
        )->setPatterns(['id' => '\d+'])
        ->setMethods(['PATCH']);

        /** Add Comment */
        $routes->connect(
            '/:id/comments',
            ['controller' => 'Posts', 'action' => 'addComment']
        )->setPatterns(['id' => '\d+'])
        ->setMethods(['POST']);

        /** Fetch Comment */
        $routes->connect(
            '/:id/comments',
            ['controller' => 'Posts', 'action' => 'fetchComments']
        )->setPatterns(['id' => '\d+'])
        ->setMethods(['GET']);

        /** Fetch Likers */
        $routes->connect(
            '/:id/likers',
            ['controller' => 'Posts', 'action' => 'fetchLikers']
        )->setPatterns(['id' => '\d+'])
        ->setMethods(['GET']);

        /** Delete Comment */
        $routes->connect(
            '/comments/:id',
            ['controller' => 'Comments', 'action' => 'delete']
        )->setPatterns(['id' => '\d+'])
        ->setMethods(['DELETE']);

        /** Share */
        $routes->connect(
            '/share/:id',
            ['controller' => 'Posts', 'action' => 'share']
        )->setPatterns(['id' => '\d+'])
        ->setMethods(['POST']);

        /** View */
        $routes->connect(
            '/:id',
            ['controller' => 'Posts', 'action' => 'view']
        )->setPatterns(['id' => '\d+'])
        ->setMethods(['GET']);

        /** Delete */
        $routes->connect(
            '/:id',
            ['controller' => 'Posts', 'action' => 'delete']
        )->setPatterns(['id' => '\d+'])
        ->setMethods(['DELETE']);

    });

    /************************
     * USERS
     */
    $routes->prefix('users', function (RouteBuilder $routes) {
        // Profiles
        $routes->connect(
            '/:username',
            ['controller' => 'Users', 'action' => 'profile']
        )
        ->setMethods(['GET']);
        // Update Profile
        $routes->connect(
            '/',
            ['controller' => 'Users', 'action' => 'updateUser']
        )
        ->setMethods(['PUT']);
        // Update Profile Image
        $routes->connect(
            '/update-image',
            ['controller' => 'Users', 'action' => 'updateImage']
        )
        ->setMethods(['POST']);
        // Mutual
        $routes->connect(
            '/:username/mutual',
            ['controller' => 'Users', 'action' => 'mutual']
        )
        ->setMethods(['GET']);
        // Followers
        $routes->connect(
            '/:username/is-following',
            ['controller' => 'Users', 'action' => 'isFollowing']
        )->setMethods(['GET']);

        $routes->connect(
            '/follow/recommended',
            ['controller' => 'Users', 'action' => 'recommended']
        )
        ->setMethods(['GET']);

        $routes->connect(
            '/:id/followers',
            ['controller' => 'Users', 'action' => 'fetchFollowers']
        )
        ->setMethods(['GET']);

        $routes->connect(
            '/:id/following',
            ['controller' => 'Users', 'action' => 'fetchFollowing']
        )
        ->setMethods(['GET']);

        $routes->connect(
            '/:username/followers/count',
            ['controller' => 'Users', 'action' => 'countFollowers']
        );

        $routes->connect(
            '/:username/following/count',
            ['controller' => 'Users', 'action' => 'countFollowing']
        )->setMethods(['GET']);

        $routes->connect(
            '/:username/follow/count',
            ['controller' => 'Users', 'action' => 'countFollow']
        )->setMethods(['GET']);

        $routes->connect(
            '/:id/follow',
            ['controller' => 'Users', 'action' => 'follow']
        )
        ->setPatterns(['id' => '\d+'])
        ->setMethods(['POST']);
    });

    /************************
     * SEARCH
     */
    $routes->connect('/search', ['controller' => 'Search', 'action' => 'index']);
    $routes->connect('/search/users', ['controller' => 'Search', 'action' => 'users']);
    $routes->connect('/search/posts', ['controller' => 'Search', 'action' => 'posts']);
    $routes->connect('/search/test', ['controller' => 'Search', 'action' => 'test']);

    /************************
     * NOTIFICATIONS
     */
    $routes->connect(
        '/notifications/unread',
        ['controller' => 'Notifications', 'action' => 'fetchUnread']
    )
    ->setMethods(['GET']);

    $routes->connect(
        '/notifications/unread/count',
        ['controller' => 'Notifications', 'action' => 'countUnread']
    )
    ->setMethods(['GET']);

    $routes->connect(
        '/notifications/read',
        ['controller' => 'Notifications', 'action' => 'fetchRead']
    )
    ->setMethods(['GET']);

    $routes->connect(
        '/notifications/read',
        ['controller' => 'Notifications', 'action' => 'readAll']
    )
    ->setMethods(['POST']);

    $routes->connect(
        '/notifications/read/:id',
        ['controller' => 'Notifications', 'action' => 'readOne']
    )
    ->setPatterns(['id' => '\d+'])
    ->setMethods(['POST']);
});

Router::scope('/', function (RouteBuilder $routes) {
    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
    $routes->connect('/*', ['controller' => 'Pages', 'action' => 'display']);

    $routes->fallbacks(DashedRoute::class);
});