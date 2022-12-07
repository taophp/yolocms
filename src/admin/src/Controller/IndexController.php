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

        [$head, $body] = $this->parse($srcFullFileName);

        /**
         * @todo make the default theme web configurable
         */
        return $this->render('index.html.twig', [
            'title' => $head->title,
            'body'  => $body,
            'adminbar'   => $this->getAdminBar(),
        ]);
    }

    protected function getAdminBar() : String {
        return $this->renderView('@admin/adminbar.html.twig');
    }

    #[Route('/_build', name: '_build', priority: 1)]
    public function build() : Response {
        $body = '';

        $dstDir = realpath(__DIR__ . '/../../build');

        // Copying assets
        $assetsDir = realpath(__DIR__ . '/../../themes/default/assets');
        static::rcopy($assetsDir, $dstDir . '/assets');

        // Build contents
        $srcDir =  realpath(__DIR__.'/../../contents');
        $srcFiles = glob($srcDir . '/*', GLOB_MARK);

        foreach ($srcFiles as $srcFile) {
            dump($srcFile);
            if (in_array(substr(basename($srcFile),0,1),['_','.'])) { // ignore
                dump('Continue:' . __LINE__);
                continue;
            }
            $dstFile = str_replace($srcDir, $dstDir, $srcFile);
            if (is_dir($srcFile)) { // make directory if not exists
                if (!file_exists($dstFile)) {
                    mkdir($dstFile, '0777', true);
                }
                dump('Continue:' . __LINE__);
                continue;
            }
            if (substr($srcFile,-3)==='.md') { // parse
                [$head, $body] = $this->parse($srcFile);
                $dstFile = substr($dstFile, 0, strrpos($dstFile, '.md')) . '.' . (empty($head->layout) ? 'html' : pathinfo($head->layout, PATHINFO_EXTENSION));
                dump('Parse', $dstFile);
                file_put_contents($dstFile, $this->renderView(empty($head->layout) ? 'index.html.twig' : $head->layout, [
                    'title' => $head->title,
                    'body' => $body,
                ]));
                continue;
            }
            // copy
            copy($srcFile, $dstFile);
        }

        return $this->render('index.html.twig', [
            'title' => 'Build',
            'body'  => $body,
            'adminbar'   => $this->getAdminBar(),
        ]);
    }
 
    public function parse(string $filename) :  array {
        $srcContents = file_get_contents($filename);

        try {
            [$head, $body] = explode("\n---\n", $srcContents, 2);
        } catch (\ErrorException $e) {
            if ($e->getMessage()==='Warning: Undefined array key 1') {
                throw new \ErrorException('Invalid source file format. File: '.$filename);
            }else{
                throw $e;
            }
        }

        $converter = new CommonMarkConverter();
        return [Yaml::parse($head,Yaml::PARSE_OBJECT_FOR_MAP), $converter->convert($body)];
        
    }

    public static function rcopy(string $src, string $dst) {
        if (is_dir($src)) {
            if (!file_exists($dst)) {
                mkdir($dst);
            }
            $files = scandir($src);
            foreach ($files as $file)
            if ($file != "." && $file != "..") static::rcopy("$src/$file", "$dst/$file");
        } else if (file_exists($src)) {
            if (file_exists($dst) && filemtime($dst) >= filemtime($src)) {
                return;
            }
            copy($src, $dst);
        }
      }
}