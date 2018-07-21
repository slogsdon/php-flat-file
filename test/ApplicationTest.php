<?php

namespace FlatFile\Tests;

use FlatFile\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /** @var Application */
    protected $app;

    public function setup()
    {
        $this->app = new Application(['noRun' => true]);
    }

    public function testResolveRouter()
    {
        $expected = realpath(__DIR__ . '/../src/router.php');
        $this->assertEquals($expected, $this->app->resolveRouter());
    }

    public function testGetOption()
    {
        $this->assertEquals(true, true === $this->app->getOption('noRun'));
    }

    public function testSetOption()
    {
        $value = 'foo';
        $this->app->setOption('bar', $value);
        $this->assertEquals($value, $this->app->getOption('bar'));
    }

    public function testFindPages()
    {
        $this->assertEquals([], $this->app->findPages());

        $this->app->setOption('pagesPath', __DIR__);
        $this->assertNotEquals([], $this->app->findPages());
    }

    public function testGetContentFor()
    {
        list($status, $body) = $this->app->getContentFor('/');
        $this->assertEquals(404, $status);
        $this->assertEquals('not found', $body);
    }
}
