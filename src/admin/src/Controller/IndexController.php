<?php
namespace App\Controller;

use Masterminds\HTML5\Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use League\CommonMark\CommonMarkConverter;
use Symfony\Component\Yaml\Yaml;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    #[Route('/{page<.+>}', name: 'index', defaults: ['page' => 'index.md'])]
    public function index(String $page): Response
    {
        $srcDir =  realpath(__DIR__.'/../../contents');
        $srcFullFileName = realpath($srcDir.'/'.$page);

        // Check the path
        if (strpos($srcFullFileName, $srcDir) !== 0) {
            throw new Exception("File not found or access refused: ".$page);
        }

        $srcContents = file_get_contents($srcFullFileName);

        try {
            [$head, $body] = explode("\n---\n", $srcContents, 2);
        } catch (\ErrorException $e) {
            if ($e->getMessage()==='Warning: Undefined array key 1') {
                throw new \ErrorException('Invalid source file format. File: '.$page);
            }else{
                throw $e;
            }
        }

        $itemConfig = Yaml::parse($head);

        $converter = new CommonMarkConverter();
        return $this->render('index.html.twig', [
            'title' => $itemConfig['title'],
            'body'  => $converter->convert($body),
            'adminbar'   => static::getAdminBar(),
        ]);
    }

    protected static function getAdminBar() : String {
        return '<div style="
            display: block;
            background-color: #222;
            top: 0;
            box-shadow: 0 -1px 0 rgba(0, 0, 0, 0.2);
            color: #EEE;
            font: 11px Arial, sans-serif;
            margin: 0;
            padding: .1rem 1rem;
            position: fixed;
            left: 0;
            text-align: left;
            text-transform: none;
            z-index: 99999;
            direction: ltr;
            -webkit-font-smoothing: subpixel-antialiased;
            -moz-osx-font-smoothing: auto;
            border-bottom: 1px dotted gold;
            width: 100%;
        }">toto<span style="float:right;">X</span></div>';
    }
}