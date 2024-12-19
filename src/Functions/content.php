<?php declare(strict_types=1);

namespace FlatFile\Functions;

use League\CommonMark\MarkdownConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use FlatFile\FileParser\ParsedFile;

function markdown(string $markdown): ParsedFile
{
    $environment = new Environment([]);
    $environment->addRenderer(FencedCode::class, new FencedCodeRenderer([]));
    $environment->addRenderer(IndentedCode::class, new IndentedCodeRenderer([]));
    $environment->addExtension(new CommonMarkCoreExtension());
    $converter = new MarkdownConverter($environment);

    $content = YamlFrontMatter::parse($markdown);
    $result = new ParsedFile;

    // Convert the markdown content
    $converted = $converter->convert($content->body());
    $result->content = $converted->getContent();

    /** @var array<string, string> $meta */
    $meta = $content->matter();
    if (empty($meta)) {
        $meta = [];
    }

    $result->meta = $meta;

    return $result;
}
