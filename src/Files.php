<?php declare(strict_types=1);

namespace FlatFile;

class Files
{
    /**
     * @param string $root
     *
     * @return iterable<\SplFileInfo>
     */
    public function findAll(string $root): iterable
    {
        $dir = new \RecursiveDirectoryIterator($root);
        $ite = new \RecursiveIteratorIterator($dir);
        /** @var iterable<\SplFileInfo> */
        $fileIterator = new \RegexIterator($ite, '/[^\/]*/');
        foreach ($fileIterator as $file) {
            if ($file->getFileName() === '.' || $file->getFileName() === '..') {
                continue;
            }
            yield $file;
        }
    }
}
