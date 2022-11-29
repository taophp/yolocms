<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Yaml\Yaml;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheSeer\Tokenizer\Exception;

class AssetsController extends AbstractController
{
    /**
     * List allowed asset file extensions with their mime types
     * 
     * @todo Replace by a configuration file
     * @see http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
     * @see https://symfony.com/doc/current/components/config.html
     */
    protected static $ext2mime = [
        'css'   =>  'text/css',
        'js'    =>  'text/javascript',
        'jpg'   =>  'image/jpeg',
        'jpe'   =>  'image/jpeg',
        'jpeg'  =>  'image/jpeg',
        'gif'   =>  'image/gif',
        'png'   =>  'image/png',
        'webp'  =>  'image/webp',
        'ico'  =>  'image/x-icon',    
    ];

    #[Route('/assets/{dir}/{srcFileName}', name: 'assets')]
    public function index(string $srcFileName, string $dir=''): Response
    {
        if ($dir==='admin') {
            $srcDir =  __DIR__.'/../../templates/assets/';
            $dir='';
        }else{
            $srcDir =  __DIR__.'/../../themes/default/assets/';
        }
        /**
         * @todo Use the configured theme
         */
        $srcFullFileName = realpath($srcDir.$dir.'/'.$srcFileName);
        /**
         * @todo Check if the required file is stored in the allowed path
         */
        return new Response(
            file_get_contents($srcFullFileName),
            Response::HTTP_OK,[
                'content-type' => static::ext2mime($srcFullFileName)
            ]
        );
    }

    protected static function ext2mime(string $fileName) {
        $ext = substr($fileName, strrpos($fileName,'.',)+1);
        if (array_key_exists($ext, static::$ext2mime)) {
            return (static::$ext2mime[$ext]);
        }

        throw new Exception('Unknown mime-type for asset file: '.$fileName);
    }
    
}