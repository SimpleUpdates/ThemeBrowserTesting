<?php

namespace ThemeViz;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var StubFilesystem|Filesystem */
    protected $mockFilesystem;

    /** @var StubFirefox|Firefox */
    protected $mockFirefox;

    /** @var StubGit|Git $mockGit */
    protected $mockGit;

    /** @var StubLess|Less */
    protected $mockLess;

    /** @var StubTwig|Twig */
    protected $mockTwig;

    /** @var Factory $factory */
    protected $factory;

    protected $themePath = "path/to/theme";

    /**
     * @param $minimalComponentsFile
     */
    protected $minimalComponentsFile = [
        "screens" => [
            [
                "path" => "path/to/file.twig",
                "scenarios" => [
                    "ScenarioName" => []
                ]
            ]
        ]
    ];

    protected function setUp()
    {
        parent::setUp();

        define("THEMEVIZ_BASE_PATH", dirname(__DIR__));
        define("THEMEVIZ_THEME_PATH", $this->themePath);

        $this->mockFilesystem = new StubFilesystem($this);
        $this->mockFirefox = new StubFirefox($this);
        $this->mockGit = new StubGit($this);
        $this->mockLess = new StubLess($this);
        $this->mockTwig = new StubTwig($this);

        $this->factory = new Factory(
            $this->mockFilesystem,
            $this->mockFirefox,
            $this->mockGit,
            $this->mockLess,
            $this->mockTwig
        );
    }

    protected function loadMinimalComponentsFile(): void
    {
        $this->loadComponentsFileFromArrays($this->minimalComponentsFile);
    }

    /**
     * @param $arrayData
     */
    private function loadComponentsFileFromArrays($arrayData): void
    {
        $this->mockFilesystem->setMappedReturnValues("getFile", [
            [THEMEVIZ_THEME_PATH . "/components.json", json_encode($arrayData)]
        ]);
    }
}