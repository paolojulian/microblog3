<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use App\Lib\Utils\ImageResizerHelper;

class PostHandlerComponent extends Component
{
    public $components = ['UploadImgHandler'];
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
}
