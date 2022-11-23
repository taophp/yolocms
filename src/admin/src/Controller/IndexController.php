<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Yaml\Yaml;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        $srcDir =  __DIR__.'/../../contents/';
        $srcFileName = 'index.md';
        $srcFullFileName = $srcDir.$srcFileName;
        $srcContents = file_get_contents($srcFullFileName);

        try {
            [$head, $body] = explode("\n---\n", $srcContents, 2);
        } catch (\ErrorException $e) {
            if ($e->getMessage()==='Warning: Undefined array key 1') {
                throw new \ErrorException('Invalid source file format. File: '.$srcFileName);
            }else{
                throw $e;
            }
        }

        Yaml::parse($head);

        $converter = new CommonMarkConverter();
        return new Response(
            $converter->convert($body)
        );
    }
}