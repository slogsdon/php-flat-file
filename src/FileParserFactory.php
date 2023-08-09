<?php declare(strict_types=1);

namespace FlatFile;

use FlatFile\FileParser\FileParserInterface;
use FlatFile\FileParser\MarkdownFileParser;
use FlatFile\FileParser\PhpFileParser;

/**
 * Obtains correct file parser based on file attributes
 */
class FileParserFactory
{
    /** @var array<FileParserInterface> */
    private $parsers;

    /**
     * @param array<FileParserInterface> $parsers
     */
    public function __construct(array $parsers = [])
    {
        if (empty($parsers)) {
            $parsers = $this->getDefaultParsers();
        }

        $this->parsers = $parsers;
    }

    public function createFrom(\SplFileInfo $file): FileParserInterface
    {
        foreach ($this->parsers as $parser) {
            if ($parser->canParse($file)) {
                return $parser;
            }
        }

        throw new \Exception('Unsupported file type');
    }

    /**
     * @return array<FileParserInterface>
     */
    public function getDefaultParsers(): array
    {
        return [
            new MarkdownFileParser,
            new PhpFileParser,
        ];
    }
}
