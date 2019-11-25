<?php
namespace App\Controller\Api;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\ORM\Table;

/**
 * Application Controller for Api
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Auth', [
            'storage' => 'Memory',
            'authorize' => ['Controller'],
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'username',
                        'password' => 'password',
                    ],
                ],
                'ADmad/JwtAuth.Jwt' => [
                    'parameter' => 'token',
                    'userModel' => 'Users',
                    'fields' => [
                        'username' => 'id'
                    ],
                    // Boolean indicating whether the "sub" claim of JWT payload
                    // should be used to query the Users model and get user info.
                    // If set to `false` JWT's payload is directly returned.
                    'queryDatasource' => true,
                ]
            ],

            'unauthorizedRedirect' => false,
            'checkAuthIn' => 'Controller.initialize',

            // If you don't have a login action in your application set
            // 'loginAction' to false to prevent getting a MissingRouteException.
            'loginAction' => '/api/auth/login'
        ]);
    }

    protected function jsonResponse($status = 200, $message = '', $data = [])
    {
        $this->response = $this->response->withStatus($status);
        return $this->set([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            '_serialize' => ['data', 'status', 'message']
        ]);
    }
    protected function responseOK($message = '', $data = [])
    {
        $this->jsonResponse(200, $message, $data);
    }
    protected function responseData($data = [])
    {
        $this->jsonResponse(200, '', $data);
    }
    protected function responseUnprocessableEntity($data = [], $message = '')
    {
        $this->jsonResponse(422, $message, $data);
    }
    protected function responseDeleted($message = '')
    {
        $this->jsonResponse(204, $message);
    }
    protected function responseInternalServerError()
    {
        $this->jsonResponse(500);
    }
    protected function responseCreated($data = [])
    {
        $this->jsonResponse(200, '', $data);
    }
    protected function responseNotFound()
    {
        $this->jsonResponse(404);
    }
    protected function responseForbidden()
    {
        $this->jsonResponse(403);
    }

    public function isAuthorized()
    {
        return true;
    }

    /**
     * Check if model is owned by user passed
     * !!IMPORTANT
     * table should have user_id as column name
     * for its owner
     * 
     * TODO
     * make it dynamic for any field_name
     * 
     * @return bool
     */
    public function isOwnedBy(Table $model, int $userId)
    {
        $reqId = (int) $this->request->params['id'];
        return $model->isOwnedBy($reqId, $userId);
    }
}
