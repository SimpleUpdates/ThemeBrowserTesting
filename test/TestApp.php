<?php

final class TestApp extends ThemeViz\TestCase
{
    /** @var \ThemeViz\App $app */
    private $app;

    protected function setUp()
    {
        parent::setUp();

        $this->app = $this->factory->get("App");
    }

    public function testRetrievesConfigFile()
    {
        $this->app->compile();

        $this->assertTrue($this->mockFilesystem->wasMethodCalledWith(
            "getFile",
            "path/to/theme/components.json"
        ));
    }

    public function testRendersScreens()
    {
        $this->mockFilesystem->loadComponentsFileFromFilesystem("testComponentsFile1.json");

        $this->app->compile();

        $callback = function ($carry, $call) {
            $templateFile = $call[0];

            return $carry || $templateFile === "component.twig";
        };

        $this->mockTwig->assertAnyCallMatches("renderFile", $callback);
    }

    public function testRendersGlobalLess()
    {
        $this->loadMinimalComponentsFile();

        $this->app->compile();

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

        $this->app->compile();

        $this->assertTrue($this->mockLess->wasMethodCalled("getCss"));
    }

    public function testLoadsBaseLess()
    {
        $this->mockFilesystem->loadComponentsFileFromFilesystem("testComponentsFile1.json");

        $this->app->compile();

        $this->assertTrue($this->mockFilesystem->wasMethodCalledWith(
            "getFile",
            THEMEVIZ_BASE_PATH . "/view/base.less"
        ));
    }

	/**
	 * @throws Exception
	 */
	public function testParsesBaseLess()
    {
        $this->loadMinimalComponentsFile();

        $this->mockFilesystem->setReturnValue("getFile", "base_less");

        $this->app->compile();

        $this->mockLess->assertMethodCalledWith(
            "parse",
            "base_less"
        );
    }

    public function testSavesRenderedTemplates()
    {
        $this->loadMinimalComponentsFile();

        $this->mockTwig->setReturnValue("renderFile", "rendered_layout");

        $this->app->compile();

        $this->assertTrue($this->mockFilesystem->wasMethodCalledWith(
            "fileForceContents",
            THEMEVIZ_BASE_PATH . "/build/head/html/path/to/file--ScenarioName.twig",
            "rendered_layout"
        ));
    }

    public function testDeletesBuildFolder()
    {
        $this->loadMinimalComponentsFile();

        $this->mockTwig->setReturnValue("render", "rendered_layout");

        $this->app->compile();

        $this->mockFilesystem->assertMethodCalledWith(
            "deleteTree",
            THEMEVIZ_BASE_PATH . "/build"
        );
    }

    public function testGetsThemeConf()
    {
        $this->loadMinimalComponentsFile();

        $this->app->compile();

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

        $this->app->compile();

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

        $this->app->compile();

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

        $this->app->compile();

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

        $this->app->compile();

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

        $this->app->compile();

        $this->mockTwig->assertAnyCallMatches("renderFile", function($carry, $call) {
           $data = $call[1];

           return $carry || $data["themeviz_css"] === "compiled_css";
        });
    }

    public function testCreatesShotsFolder()
    {
        $this->loadMinimalComponentsFile();

        $this->app->compile();

        $this->mockFilesystem->assertMethodCalledWith(
            "makeTree", THEMEVIZ_BASE_PATH . "/build/head/shots"
        );
    }

    public function testPassesCompiledComponentsFolderToPhotographer()
    {
        $this->loadMinimalComponentsFile();

        $this->app->compile();

        $this->mockFilesystem->assertMethodCalledWith(
            "scanDir",
            THEMEVIZ_BASE_PATH . "/build/head/html"
        );
    }

    public function testSavesState()
    {
        $this->loadMinimalComponentsFile();

        $this->app->compile();

        $this->mockGit->assertMethodCalledWith(
            "saveState",
            THEMEVIZ_THEME_PATH
        );
    }

    public function testChecksOutProduction()
    {
        $this->loadMinimalComponentsFile();

        $this->app->compile();

        $this->mockGit->assertMethodCalledWith(
            "checkoutRemoteBranch",
            THEMEVIZ_THEME_PATH,
            "production"
        );
    }

    public function testBuildsProductionBranch()
    {
        $this->loadMinimalComponentsFile();

        $this->app->compile();

        $this->mockFilesystem->assertMethodCalledWith(
            "scanDir",
            THEMEVIZ_BASE_PATH . "/build/production/html"
        );
    }

    public function testRestoresGitState()
    {
        $this->loadMinimalComponentsFile();

        $this->app->compile();

        $this->mockGit->assertMethodCalledWith(
            "resetState",
            THEMEVIZ_THEME_PATH
        );
    }

    public function testPullsProduction()
    {
        $this->loadMinimalComponentsFile();

        $this->app->compile();

        $this->mockGit->assertMethodCalledWith(
            "pull",
            THEMEVIZ_THEME_PATH,
            "production"
        );
    }

    public function testMakesDiffs()
    {
        $this->loadMinimalComponentsFile();

        $this->mockFilesystem->setReturnValue("scanDir", ["component"]);

        $this->app->compile();

        $this->mockPixelmatch->assertMethodCalled("makeDiff");
    }

    public function testCompilesSummaryPage()
    {
        $this->loadMinimalComponentsFile();

        $this->mockFilesystem->setReturnValue("scanDir", ["component"]);

        $this->app->compile();

        $this->mockTwig->assertAnyCallMatches("renderFile", function($carry, $args) {
            return $args[0] === "summary.twig" || $carry;
        });
    }

    public function testBuildHeadDeletesHeadTree()
	{
		$this->loadMinimalComponentsFile();

		$this->app->buildStyleGuide();

		$this->mockFilesystem->assertMethodCalledWith(
			"deleteTree",
			THEMEVIZ_BASE_PATH . "/build/head"
		);
	}

	public function testBuildProductionDeletesProductionTree()
	{
		$this->loadMinimalComponentsFile();

		$this->app->buildProduction();

		$this->mockFilesystem->assertMethodCalledWith(
			"deleteTree",
			THEMEVIZ_BASE_PATH . "/build/production"
		);
	}

	public function testBuildProductionChecksOutProduction()
	{
		$this->loadMinimalComponentsFile();

		$this->app->buildProduction();

		$this->mockGit->assertMethodCalled("saveState");
		$this->mockGit->assertMethodCalled("checkoutRemoteBranch");
		$this->mockGit->assertMethodCalled("pull");
		$this->mockGit->assertMethodCalled("resetState");
	}

	public function testBuildStyleGuideBuildsHead()
	{
		$this->loadMinimalComponentsFile();

		$this->app->buildStyleGuide();

		$this->mockFilesystem->assertMethodCalledWith(
			"deleteTree",
			THEMEVIZ_BASE_PATH . "/build/head"
		);
	}

	public function testBuildStyleGuideCompilesStyleGuide()
	{
		$this->loadMinimalComponentsFile();

		$this->app->buildStyleGuide();

		$this->mockTwig->assertTwigTemplateRendered("styleGuide.twig");
	}

	public function testCompileCompilesStyleGuide()
	{
		$this->loadMinimalComponentsFile();

		$this->app->compile();

		$this->mockTwig->assertTwigTemplateRendered("styleGuide.twig");
	}
}