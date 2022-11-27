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
     */
    protected static $allowedExt2mime = [
        'css' => 'text/css',
    ];

    #[Route('/assets/{dir}/{srcFileName}')]
    public function index(string $srcFileName, string $dir=''): Response
    {
        $srcDir =  __DIR__.'/../../themes/default/assets/';
        $srcFullFileName = realpath($srcDir.$dir.'/'.$srcFileName);
/**
 * @todo Check if the required file is stored in the allowed path
 */
        return new Response(
            file_get_contents($srcFullFileName),
            Response::HTTP_OK,[
                'content-type' => static::allowedExt2mime($srcFullFileName)
            ]
        );
    }

    protected static function allowedExt2mime(string $fileName) {
        $ext = substr($fileName, strrpos($fileName,'.',)+1);
        if (array_key_exists($ext, static::$allowedExt2mime)) {
            return (static::$allowedExt2mime[$ext]);
        }

        throw new Exception('Unallowed mime-type for asset file: '.$fileName);
    }
}