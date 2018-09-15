<?php declare(strict_types=1);

namespace FlatFile\FileParser;

class PhpFileParser extends FileParserAbstract
{
    public function parse(\SplFileInfo $file): \stdClass
    {
        ob_start();
        $required = include $file->getPathName();
        $output = ob_get_clean();

        return (object)[
            'content' => ($required === 1)
                ? $output
                : $required,
            'meta' => [],
        ];
    }

    public function supportedFileExtensions(): array
    {
        return ['php'];
    }
}
