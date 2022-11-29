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
        /**
         * @todo make the default theme web configurable
         */
        return $this->render('index.html.twig', [
            'title' => $itemConfig['title'],
            'body'  => $converter->convert($body),
            'adminbar'   => $this->getAdminBar(),
        ]);
    }

    protected function getAdminBar() : String {
        return $this->renderView('@admin/adminbar.html.twig');
    }
}