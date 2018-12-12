<?php

final class TestTwigCompiler extends ThemeViz\TestCase
{
    /** @var \ThemeViz\TwigCompiler $twigCompiler */
    private $twigCompiler;

    protected function setUp()
    {
        parent::setUp();

        $this->twigCompiler = $this->factory->getTwigCompiler();
    }

    public function testExists()
    {
        $this->assertTrue(class_exists("\\ThemeViz\\TwigCompiler"));
    }

    public function testCompilesCssOncePerCompilation()
    {
    	$this->mockLess->setReturnValue("getCss", "rendered_css");

        $componentsFile = [
            "screens" => [
                [
                    "path" => "path/to/file.twig",
                    "scenarios" => [
                        "ScenarioName" => []
                    ]
                ],
                [
                    "path" => "path/to/file2.twig",
                    "scenarios" => [
                        "ScenarioName" => []
                    ]
                ]
            ]
        ];

        $this->mockFilesystem->loadComponentsFile($componentsFile);

        $this->twigCompiler->compileTwig();

        $this->mockLess->assertCallCount("getCss", 1);
    }

	public function testCompilesComponentWithNoScenarios()
	{
		$componentsFile = [
			"screens" => [
				[
					"path" => "path/to/file.twig"
				]
			]
		];

		$this->mockFilesystem->loadComponentsFile($componentsFile);

		$this->twigCompiler->compileTwig();

		$this->mockTwig->assertMethodCalled("renderFile");
	}

	public function testCompilesComponentWithDataObject()
	{
		$this->loadMinimalComponentsFile();

		$this->twigCompiler->compileTwig();

		$calls = $this->mockTwig->getCalls("renderFile");

		$this->assertInstanceOf("\\ThemeViz\\Data", $calls[0][1]);
	}
}