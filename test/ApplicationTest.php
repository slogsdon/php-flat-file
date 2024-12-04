<?php

namespace FlatFile\Tests;

use FlatFile\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /** @var string */
    const EXPECTED_CONTENT = "<h1>content</h1>\n";
    /** @var string */
    const APP_OPTION_PAGES_PATH = 'pagesPath';
    /** @var string */
    const FIXTURES_PATH_SITE = '/fixtures/site';

    /** @var Application */
    protected $app;

    public function setup(): void
    {
        $this->app = new Application(['noRun' => true]);
    }

    public function testResolveRouter(): void
    {
        $expected = realpath(__DIR__ . '/../src/router.php');
        $this->assertEquals($expected, $this->app->resolveRouter());
    }

    public function testGetOption(): void
    {
        $this->assertEquals(true, true === $this->app->getOption('noRun'));
    }

    public function testSetOption(): void
    {
        $value = 'foo';
        $this->app->setOption('bar', $value);
        $this->assertEquals($value, $this->app->getOption('bar'));
    }

    public function testFindPages(): void
    {
        $this->assertEquals([], $this->app->findPages());

        $this->app->setOption(static::APP_OPTION_PAGES_PATH, __DIR__);
        $this->assertNotEquals([], $this->app->findPages());
    }

    public function testGetContentFor(): void
    {
        list($status, $body) = $this->app->getContentFor('/');
        $this->assertEquals(404, $status);
        $this->assertEquals('not found', $body);
    }

    public function testPhpEchoContent(): void
    {
        $this->app->setOption(static::APP_OPTION_PAGES_PATH, __DIR__ . static::FIXTURES_PATH_SITE);
        $this->app->prepareTemplates();
        $pages = $this->app->findPages();
        $this->assertNotEquals([], $pages);

        $content = $pages['echos-content']->content;
        $body = $content($this->app);
        assert($body instanceof \FlatFile\FileParser\ParsedFile);
        $this->assertEquals(static::EXPECTED_CONTENT, $body->content);
    }

    public function testMarkdownContent(): void
    {
        $this->app->setOption(static::APP_OPTION_PAGES_PATH, __DIR__ . static::FIXTURES_PATH_SITE);
        $pages = $this->app->findPages();
        $this->assertNotEquals([], $pages);

        $content = $pages['markdown-content']->content;
        $body = $content($this->app);
        assert($body instanceof \FlatFile\FileParser\ParsedFile);
        $this->assertEquals(static::EXPECTED_CONTENT, $body->content);
    }
}
