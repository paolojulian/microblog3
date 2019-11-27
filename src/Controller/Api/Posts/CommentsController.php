<?php
namespace App\Controller\Api\Posts;

use App\Controller\Api\AppController;
use Cake\Event\Event;

/**
 * Api/Post/Posts Controller
 *
 *
 * @method \App\Model\Entity\Api/Post/Post[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CommentsController extends AppController
{
    public function isAuthorized()
    {
        if ( ! in_array($this->request->getParam('action'), ['delete', 'update'])) {
            return true;
        }

        if ( ! parent::isOwnedBy($this->Comments, $this->Auth->user('id'))) {
            return false;
        }

        return parent::isAuthorized();
    }

    /**
     * [DELETE]
     * [PRIVATE]
     * 
     * @return status 204 - No Content
     */
    public function delete()
    {
        $this->request->allowMethod('delete');
        $commentId = (int) $this->request->getParam('id');
        $this->Comments->deleteComment($commentId);
        return $this->responseDeleted();
    }
}
