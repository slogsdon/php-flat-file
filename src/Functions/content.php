<?php declare(strict_types=1);

namespace FlatFile\Functions;

use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Element\IndentedCode;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use FlatFile\FileParser\ParsedFile;

function markdown(string $markdown): ParsedFile
{
    static $converter;
    if (!$converter) {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addBlockRenderer(FencedCode::class, new FencedCodeRenderer([]));
        $environment->addBlockRenderer(IndentedCode::class, new IndentedCodeRenderer([]));

        $converter = new CommonMarkConverter([], $environment);
    }

    $content = YamlFrontMatter::parse($markdown);
    $result = new ParsedFile;
    $result->content = $converter->convertToHtml($content->body());
    $result->meta = $content->matter();
    return $result;
}
