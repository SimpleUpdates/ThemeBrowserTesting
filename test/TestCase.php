<?php

namespace ThemeViz;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var StubFilesystem|Filesystem */
    protected $mockFilesystem;

    /** @var StubLess|Less */
    protected $mockLess;

    /** @var StubTwig|Twig */
    protected $mockTwig;

    /** @var Factory $factory */
    protected $factory;

    protected $themePath = "path/to/theme";

    protected function setUp()
    {
        parent::setUp();

        define("THEMEVIZ_BASE_PATH", dirname(__DIR__));
        define("THEMEVIZ_THEME_PATH", $this->themePath);

        $this->mockFilesystem = new StubFilesystem();
        $this->mockLess = new StubLess();
        $this->mockTwig = new StubTwig();

        $this->factory = new Factory(
            $this->mockFilesystem,
            $this->mockLess,
            $this->mockTwig
        );
    }
}