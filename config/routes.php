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
use Cake\Http\Middleware\CsrfProtectionMiddleware;
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

    $routes->setExtensions(['json', 'xml']);
    $routes->prefix('auth', function (RouteBuilder $routes) {
        $routes->connect('/login', ['controller' => 'Auths', 'action' => 'login']);
        $routes->connect('/register', ['controller' => 'Auths', 'action' => 'register']);
        $routes->connect('/activate/:key', ['controller' => 'Auths', 'action' => 'activate']);
        $routes->connect('/me', ['controller' => 'Auths', 'action' => 'me']);
    });
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
    $routes->prefix('users', function (RouteBuilder $routes) {
        // Profiles
        $routes->connect(
            '/:username',
            ['controller' => 'Users', 'action' => 'profile']
        );
        // Update Profile
        $routes->connect(
            '/',
            ['controller' => 'Users', 'action' => 'updateUser']
        )
        ->setMethods(['PUT']);
        // Mutual
        $routes->connect(
            '/:username/mutual',
            ['controller' => 'Users', 'action' => 'mutual']
        );
        // Followers
        $routes->connect(
            '/:username/is-following',
            ['controller' => 'Users', 'action' => 'isFollowing']
        )->setMethods(['GET']);

        $routes->connect(
            '/follow/recommended',
            ['controller' => 'Users', 'action' => 'recommended']
        );

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

    $routes->connect('/search', ['controller' => 'Search', 'action' => 'index']);
    // Notifications
});

Router::scope('/', function (RouteBuilder $routes) {
    // // Register scoped middleware for in scopes.
    // $routes->registerMiddleware('csrf', new CsrfProtectionMiddleware([
    //     'httpOnly' => true
    // ]));

    // /**
    //  * Apply a middleware to the current route scope.
    //  * Requires middleware to be registered via `Application::routes()` with `registerMiddleware()`
    //  */
    // $routes->applyMiddleware('csrf');

    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
    $routes->connect('/*', ['controller' => 'Pages', 'action' => 'display']);

    $routes->fallbacks(DashedRoute::class);
});