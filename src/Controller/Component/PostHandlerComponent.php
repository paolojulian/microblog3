<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use App\Lib\Utils\ImageResizerHelper;
use App\Lib\DTO\NotificationDTO;
use App\Model\Entity\Like;

class PostHandlerComponent extends Component
{
    public $components = ['UploadImgHandler', 'NotificationHandler'];
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function uploadImage($file)
    {
        $path = 'posts/';
        $uploadedFile = $this->UploadImgHandler->uploadImage(
            $file,
            $path
        );
        $imageName = $uploadedFile['imageName'];
        $imageResizer = new ImageResizerHelper("$path$imageName.png");
        $imageResizer->multipleResizeMaxHeight(
            $path.$imageName,
            [512, 256]
        );

        return $uploadedFile['basePath'];
    }

    public function notifyAfterLike(Like $like)
    {
        $this->Posts = TableRegistry::get('Posts');
        $post = $this->Posts->get($like->post_id, ['fields' => ['user_id']]);
        $notificationDTO = new NotificationDTO(
            $this->NotificationHandler::LIKED,
            $like->user_id,
            $post->user_id,
            $like->post_id
        );
        $this->NotificationHandler->notifyUser($notificationDTO);
    }
}
