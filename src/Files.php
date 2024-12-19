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
            $ignores = ['.','..','.DS_Store'];
            if (in_array($file->getFileName(), $ignores)) {
                continue;
            }
            yield $file;
        }
    }
}
