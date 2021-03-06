<?php

namespace ThemeViz;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
	/** @var StubDoiuse $mockDoiuse */
	protected $mockDoiuse;

    /** @var StubFilesystem|Filesystem */
    protected $mockFilesystem;

    /** @var StubFirefox|Firefox */
    protected $mockFirefox;

    /** @var StubGit|Git $mockGit */
    protected $mockGit;

    /** @var StubLess|Less */
    protected $mockLess;

    /** @var StubPixelmatch|Pixelmatch */
    protected $mockPixelmatch;

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

        $this->factory = new Factory(
        	$this->mockDoiuse = new StubDoiuse($this),
			$this->mockFilesystem = new StubFilesystem($this),
			$this->mockFirefox = new StubFirefox($this),
			$this->mockGit = new StubGit($this),
			$this->mockLess = new StubLess($this),
			$this->mockPixelmatch = new StubPixelmatch($this),
			$this->mockTwig = new StubTwig($this)
        );

        $this->mockFilesystem->loadThemeConf([]);
        $this->mockFilesystem->loadComponentsFile([]);
    }

    protected function loadMinimalComponentsFile(): void
    {
        $this->mockFilesystem->loadMinimalComponentsFile();
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