<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use App\Lib\Utils\ImageResizerHelper;
use App\Lib\DTO\NotificationDTO;
use App\Model\Entity\Like;
use App\Model\Entity\Comment;
use App\Model\Entity\Post;

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
        // Get the owner of the post
        $post = $this->Posts->get($like->post_id, ['fields' => ['user_id']]);
        $notificationDTO = new NotificationDTO();
        $notificationDTO->setLiked(
            $like->user_id,
            $post->user_id,
            $like->post_id
        );
        $this->NotificationHandler->notifyUser($notificationDTO);
    }

    public function notifyAfterComment(Comment $comment)
    {
        $this->Posts = TableRegistry::get('Posts');
        // Get the owner of the post
        $post = $this->Posts->get($comment->post_id, ['fields' => ['user_id']]);
        $notificationDTO = new NotificationDTO();
        $notificationDTO->setCommented(
            $comment->user_id, // user ID
            $post->user_id, // Receiver ID
            $comment->post_id
        );
        $this->NotificationHandler->notifyUser($notificationDTO);
    }

    public function notifyAfterShare(Post $post)
    {
        $this->Posts = TableRegistry::get('Posts');
        // Get the owner of the post
        $originalPost = $this->Posts->get($post->retweet_post_id, ['fields' => ['user_id']]);
        $notificationDTO = new NotificationDTO();
        $notificationDTO->setShared(
            $post->user_id,
            $originalPost->user_id,
            $post->retweet_post_id
        );
        $this->NotificationHandler->notifyUser($notificationDTO);
    }
}
