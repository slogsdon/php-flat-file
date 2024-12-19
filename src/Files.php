<?php declare(strict_types=1);

namespace FlatFile;

class Files
{
    public function findAll(string $root): iterable
    {
        $dir = new \RecursiveDirectoryIterator($root);
        $ite = new \RecursiveIteratorIterator($dir);
        $fileIterator = new \RegexIterator($ite, '/[^\/]*/');
        foreach ($fileIterator as $file) {
            $ignores = ['.','..','.DS_Store'];
            if (in_array($file->getFileName(), $ignores)) {
                continue;
            }
            yield $file;
        }
    }
}
