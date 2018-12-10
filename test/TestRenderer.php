<?php

final class TestRenderer extends ThemeViz\TestCase
{
    /** @var \ThemeViz\Renderer $renderer */
    private $renderer;

    protected function setUp()
    {
        parent::setUp();

        $this->renderer = $this->factory->getRenderer();
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
        $this->mockFilesystem->loadComponentsFileFromFilesystem("testComponentsFile1.json");

        $this->renderer->compile();

        $callback = function ($carry, $call) {
            $templateFile = $call[0];

            return $carry || $templateFile === "component.twig";
        };

        $this->mockTwig->assertAnyCallMatches("renderFile", $callback);
    }

    public function testRendersGlobalLess()
    {
        $this->loadMinimalComponentsFile();

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
        $this->loadMinimalComponentsFile();

        $this->renderer->compile();

        $this->assertTrue($this->mockLess->wasMethodCalled("getCss"));
    }

    public function testLoadsBaseLess()
    {
        $this->mockFilesystem->loadComponentsFileFromFilesystem("testComponentsFile1.json");

        $this->renderer->compile();

        $this->assertTrue($this->mockFilesystem->wasMethodCalledWith(
            "getFile",
            THEMEVIZ_BASE_PATH . "/view/base.less"
        ));
    }

    public function testParsesBaseLess()
    {
        $this->loadMinimalComponentsFile();

        $this->mockFilesystem->setReturnValue("getFile", "base_less");

        $this->renderer->compile();

        $this->assertTrue($this->mockLess->wasMethodCalledWith(
            "parse",
            "base_less"
        ));
    }

    public function testSavesRenderedTemplates()
    {
        $this->loadMinimalComponentsFile();

        $this->mockTwig->setReturnValue("renderFile", "rendered_layout");

        $this->renderer->compile();

        $this->assertTrue($this->mockFilesystem->wasMethodCalledWith(
            "fileForceContents",
            THEMEVIZ_BASE_PATH . "/build/pull/html/path/to/file--ScenarioName.twig",
            "rendered_layout"
        ));
    }

    public function testDeletesBuildFolder()
    {
        $this->loadMinimalComponentsFile();

        $this->mockTwig->setReturnValue("render", "rendered_layout");

        $this->renderer->compile();

        $this->mockFilesystem->assertMethodCalledWith(
            "deleteTree",
            THEMEVIZ_BASE_PATH . "/build"
        );
    }

    public function testGetsThemeConf()
    {
        $this->loadMinimalComponentsFile();

        $this->renderer->compile();

        $this->mockFilesystem->assertMethodCalledWith("getFile", $this->themePath . "/theme.conf");
    }

    public function testLoadsThemeConfColors()
    {
        $this->mockFilesystem->loadComponentsFileFromFilesystem("testComponentsFile1.json");

        $this->mockFilesystem->loadThemeConf([
            "config" => [
                "headerBlockColor" => [
                    "title" => "Header Background Color",
                    "type" => "color",
                    "value" => "#525252"
                ]
            ]
        ]);

        $this->renderer->compile();

        $this->mockLess->assertCallsContain(
            "parse",
            "@config-headerBlockColor: #525252;"
        );
    }

    public function testLoadsThemeConfImages()
    {
        $this->mockFilesystem->loadComponentsFileFromFilesystem("testComponentsFile1.json");

        $this->mockFilesystem->loadThemeConf([
            "config" => [
                "headlineHomeBgImage" => [
                    "title" => "Default Home - home page alternative to Carousel using the featured image option",
                    "type" => "image",
                    "value" => "{{ su.misc.privatelabel }}/hero_image.jpg"
                ]
            ]
        ]);

        $this->renderer->compile();

        $this->mockLess->assertCallsContain(
            "parse",
            "@config-headlineHomeBgImage: '".THEMEVIZ_THEME_PATH."/asset/su/hero_image.jpg';"
        );
    }

    public function testLoadsThemeConfMultipleTypeProperties()
    {
        $this->mockFilesystem->loadComponentsFileFromFilesystem("testComponentsFile1.json");

        $this->mockFilesystem->loadThemeConf([
            "config" => [
                "headerAlignment" => [
                    "title" => "Header alignment",
                    "type" => "multiple",
                    "required" => true,
                    "options" => [
                        "Left",
                        "Center"
                    ],
                    "value" => "Center"
                ]
            ]
        ]);

        $this->renderer->compile();

        $this->mockLess->assertCallsContain(
            "parse",
            "@config-headerAlignment: Center;"
        );
    }

    public function testLoadsBootstrap()
    {
        $this->loadMinimalComponentsFile();

        $this->mockFilesystem->loadThemeConf([
            "depends" => [
                "settings" => [
                    "global_bootstrap" => true
                ]
            ]
        ]);

        $this->renderer->compile();

        $this->mockTwig->assertAnyCallMatches("renderFile", function ($carry, $call) {
            $data = $call[1];
            $useBootstrap = $data["themeviz_use_bootstrap"] ?? FALSE;

            return $carry || $useBootstrap;
        });
    }

    public function testSendsCssWithTwigData()
    {
        $this->loadMinimalComponentsFile();

        $this->mockLess->setReturnValue("getCss", "compiled_css");

        $this->renderer->compile();

        $this->mockTwig->assertAnyCallMatches("renderFile", function($carry, $call) {
           $data = $call[1];

           return $carry || $data["themeviz_css"] === "compiled_css";
        });
    }

    public function testCreatesShotsFolder()
    {
        $this->loadMinimalComponentsFile();

        $this->renderer->compile();

        $this->mockFilesystem->assertMethodCalledWith(
            "makeTree", THEMEVIZ_BASE_PATH . "/build/pull/shots"
        );
    }

    public function testPassesCompiledComponentsFolderToPhotographer()
    {
        $this->loadMinimalComponentsFile();

        $this->renderer->compile();

        $this->mockFilesystem->assertMethodCalledWith(
            "scanDir",
            THEMEVIZ_BASE_PATH . "/build/pull/html"
        );
    }

    public function testSavesState()
    {
        $this->loadMinimalComponentsFile();

        $this->renderer->compile();

        $this->mockGit->assertMethodCalledWith(
            "saveState",
            THEMEVIZ_THEME_PATH
        );
    }

    public function testChecksOutProduction()
    {
        $this->loadMinimalComponentsFile();

        $this->renderer->compile();

        $this->mockGit->assertMethodCalledWith(
            "checkoutRemoteBranch",
            THEMEVIZ_THEME_PATH,
            "production"
        );
    }

    public function testBuildsProductionBranch()
    {
        $this->loadMinimalComponentsFile();

        $this->renderer->compile();

        $this->mockFilesystem->assertMethodCalledWith(
            "scanDir",
            THEMEVIZ_BASE_PATH . "/build/production/html"
        );
    }

    public function testRestoresGitState()
    {
        $this->loadMinimalComponentsFile();

        $this->renderer->compile();

        $this->mockGit->assertMethodCalledWith(
            "resetState",
            THEMEVIZ_THEME_PATH
        );
    }

    public function testPullsProduction()
    {
        $this->loadMinimalComponentsFile();

        $this->renderer->compile();

        $this->mockGit->assertMethodCalledWith(
            "pull",
            THEMEVIZ_THEME_PATH,
            "production"
        );
    }

    public function testDoesNotCheckoutRemoteBranchIfLocalBranchAvailable()
    {
        $this->loadMinimalComponentsFile();

        $this->mockGit->setReturnValue("checkoutBranch", true);

        $this->renderer->compile();

        $this->mockGit->assertMethodNotCalled("checkoutRemoteBranch");
    }

    public function testMakesDiffs()
    {
        $this->loadMinimalComponentsFile();

        $this->mockFilesystem->setReturnValue("scanDir", ["component"]);

        $this->renderer->compile();

        $this->mockPixelmatch->assertMethodCalled("makeDiff");
    }

    public function testCompilesSummaryPage()
    {
        $this->loadMinimalComponentsFile();

        $this->mockFilesystem->setReturnValue("scanDir", ["component"]);

        $this->renderer->compile();

        $this->mockTwig->assertAnyCallMatches("renderFile", function($carry, $args) {
            return $args[0] === "summary.twig" || $carry;
        });
    }
}