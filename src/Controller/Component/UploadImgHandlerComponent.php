<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Utility\Security;
use App\Lib\Utils\FileUploadHelper;

/**
 * UploadImgHandler component
 */
class UploadImgHandlerComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function uploadImage($file, $path)
    {
        try {
            if ($file['error']) {
                throw new InternalErrorException();
            }
            $imageName = Security::hash(Security::randomBytes(5) . $path . time());
            $imgPath = "img/$path";
            $fullPath = WWW_ROOT . $imgPath;
            $image = FileUploadHelper::uploadImg(
                $fullPath,
                $file,
                $imageName.'.png'
            );
        } catch (Exception $e) {
            throw $e;
        }
        return [
            'basePath' => "/app/webroot/$imgPath$imageName",
            'imageName' => $imageName
        ];
    }
}
