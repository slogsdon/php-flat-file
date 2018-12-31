<?php declare(strict_types=1);

namespace FlatFile\FileParser;

class PhpFileParser extends FileParserAbstract
{
    public function parse(\SplFileInfo $file, \League\Plates\Engine $plates = null): ParsedFile
    {
        $result = new ParsedFile;
        $result->content =
            $plates !== null
                ? $plates->render(str_replace(
                    [trim($plates->getDirectory(), '/') . '/', '.' . $file->getExtension()],
                    ['', ''],
                    $file->getPathName()
                ))
                : $result->content;
        return $result;
    }

    public function supportedFileExtensions(): array
    {
        return ['php'];
    }
}
