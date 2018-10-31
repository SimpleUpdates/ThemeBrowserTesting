<?php

final class TestRenderer extends ThemeViz\TestCase
{
    /** @var \ThemeViz\Renderer $renderer */
    private $renderer;

    /**
     * @param $configFile
     */
    private $minimalConfig = [
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

        $this->renderer = $this->factory->getRenderer();
    }

    /**
     * @param $needle
     */
    private function assertAnyPersistedFileContains($needle): void
    {
        $this->assertTrue($this->mockFilesystem->doCallsContain("fileForceContents", $needle));
    }

    public function loadConfigFile($configFile): void
    {
        $this->mockFilesystem->setReturnValue(
            "getFile",
            file_get_contents(__DIR__ . "/{$configFile}")
        );
    }

    private function loadMinimalConfig(): void
    {
        $this->loadConfig($this->minimalConfig);
    }

    /**
     * @param $config
     */
    private function loadConfig($config): void
    {
        $this->mockFilesystem->setReturnValue("getFile", json_encode($config));
    }

    public function testRetrievesConfigFile()
    {
        $this->renderer->compile();

        $this->assertTrue($this->mockFilesystem->wasMethodCalledWith(
            "getFile",
            "path/to/theme/components.json"
        ));
    }

    public function testRendersScreens()
    {
        $this->loadConfigFile("testComponentsFile1.json");

        $this->renderer->compile();

        $this->assertTrue($this->mockTwig->wasMethodCalledWith(
            "render",
            "partial/atom-sitename.html",
            [
                "sitename" => "My Site",
                "su" => [
                    "footer" => "<span>The SimpleUpdates Footer</span>",
                    "misc" => ["privatelabel" => "SU"]
                ]
            ]
        ));
    }

    public function testRendersGlobalLess()
    {
        $this->loadMinimalConfig();

        $this->renderer->compile();

        $this->assertTrue(
            $this->mockLess->wasMethodCalledWith(
                "parseFile",
                "$this->themePath/style/global.less",
                $this->themePath
            )
        );
    }

    public function testGetsCss()
    {
        $this->loadMinimalConfig();

        $this->renderer->compile();

        $this->assertTrue($this->mockLess->wasMethodCalled("getCss"));
    }

    public function testLoadsDefaultLessVariables()
    {
        $this->loadConfigFile("testComponentsFile1.json");

        $this->renderer->compile();

        $this->assertTrue($this->mockLess->doCallsContain(
            "parse",
            "@config-headerBlockColor: black;"
        ));
    }

    public function testLoadsBaseLess()
    {
        $this->loadConfigFile("testComponentsFile1.json");

        $this->renderer->compile();

        $this->assertTrue($this->mockFilesystem->wasMethodCalledWith(
            "getFile",
            THEMEVIZ_BASE_PATH . "/base.less"
        ));
    }

    public function testParsesBaseLess()
    {
        $this->loadMinimalConfig();

        $this->mockFilesystem->setReturnValueAt(1, "getFile", "base_less");

        $this->renderer->compile();

        $this->assertTrue($this->mockLess->wasMethodCalledWith(
            "parse",
            "base_less"
        ));
    }

    public function testGetIcon()
    {
        $result = $this->renderer->getIcon("icon-name");

        $this->assertContains("icon-name", $result);
    }

    public function testRegistersGetIconFunction()
    {
        $this->renderer->compile();

        $this->assertTrue($this->mockTwig->wasMethodCalledWith(
            "registerFunction",
            "getIcon",
            [$this->renderer, "getIcon"],
            ["is_safe" => ["html"]]
        ));
    }

    public function testSavesRenderedTemplates()
    {
        $this->loadMinimalConfig();

        $this->mockTwig->setReturnValue("render", "rendered_layout");

        $this->renderer->compile();

        $this->assertTrue($this->mockFilesystem->wasMethodCalledWith(
            "fileForceContents",
            THEMEVIZ_BASE_PATH . "/build/path/to/file--ScenarioName.twig",
            "<style></style><div class=''>rendered_layout</div>"
        ));
    }

    public function testDeletesBuildFolder()
    {
        $this->loadMinimalConfig();

        $this->mockTwig->setReturnValue("render", "rendered_layout");

        $this->renderer->compile();

        $this->assertTrue($this->mockFilesystem->wasMethodCalledWith(
            "deleteTree",
            THEMEVIZ_BASE_PATH . "/build"
        ));
    }

    public function testDoesNotDeleteBuildFolderIfNoScenariosToPersist()
    {
        $this->renderer->compile();

        $this->assertFalse($this->mockFilesystem->wasMethodCalled("deleteTree"));
    }

    public function testIncludesStyleTags()
    {
        $this->loadMinimalConfig();

        $this->mockTwig->setReturnValue("render", "rendered_layout");

        $this->renderer->compile();

        $this->assertAnyPersistedFileContains("<style>");
    }

    public function testOutputsCss()
    {
        $this->loadMinimalConfig();

        $this->mockTwig->setReturnValue("render", "rendered_layout");

        $this->mockLess->setReturnValue("getCss","compiled_less");

        $this->renderer->compile();

        $this->assertAnyPersistedFileContains("compiled_less");
    }

    public function testOutputsWrapperClasses()
    {
        $config = $this->minimalConfig;
        $config["wrapperClasses"] = ["su_bootstrap_safe"];
        $this->loadConfig($config);

        $this->mockTwig->setReturnValue("render", "rendered_layout");

        $this->renderer->compile();

        $this->assertAnyPersistedFileContains("<div class='su_bootstrap_safe'>");
    }
}