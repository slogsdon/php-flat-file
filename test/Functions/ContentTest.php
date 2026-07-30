<?php

namespace FlatFile\Tests\Functions;

use function FlatFile\Functions\markdown;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    public function testMarkdownSucceeds(): void
    {
        $expected = "<h1>hello</h1>\n";
        $actual = markdown('# hello')->content;
        $this->assertEquals($expected, $actual);
    }

    public function testMarkdownRendersTables(): void
    {
        $actual = markdown("| a | b |\n|---|---|\n| 1 | 2 |")->content;
        $this->assertStringContainsString('<table>', $actual);
        $this->assertStringContainsString('<th>a</th>', $actual);
        $this->assertStringContainsString('<td>1</td>', $actual);
    }
}
