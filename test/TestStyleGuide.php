<?php

final class TestStyleGuide extends ThemeViz\TestCase
{
	/** @var \ThemeViz\Page\StyleGuide $styleGuide */
	private $styleGuide;

	protected function setUp()
	{
		parent::setUp();

		$this->styleGuide = $this->factory->getFile_Page_StyleGuide();
	}

	public function testExists()
	{
		$this->assertInstanceOf("\\ThemeViz\\File\\Page\\StyleGuide", $this->styleGuide);
	}

	public function testExtendsPage()
	{
		$this->assertInstanceOf("\\ThemeViz\\File\\Page", $this->styleGuide);
	}

	public function testPassesComponentsToTemplate()
	{
		$this->mockFilesystem->setReturnValue("findPathsMatchingRecursive", ["/path/to/build/file"]);

		$this->styleGuide->compile();

		$data = [
			"themeviz_components" => [
				[
					"name" => "file",
					"html" => "file"
				]
			],
			"themeviz_css" => null
		];

		$this->mockTwig->assertMethodCalledWith(
			"renderFile",
			"styleGuide.twig",
			$data
		);
	}

	public function testGetsHtmlFiles()
	{
		$this->styleGuide->compile();

		$this->mockFilesystem->assertMethodCalledWith(
			"findPathsMatchingRecursive",
			THEMEVIZ_BASE_PATH . "/build/head/html",
			"/\.html$/"
		);
	}

	public function testSimplifiesComponentNameAndPath()
	{
		$path = "/Users/work/ProgrammingProjects/ThemeViz/build/head/html/partial/atom-sitename--Configured.html";

		$this->mockFilesystem->setReturnValue(
			"findPathsMatchingRecursive",
			[$path]
		);

		$this->styleGuide->compile();

		$data = [
			"themeviz_components" => [
				[
					"name" => "atom-sitename--Configured.html",
					"html" => "head/html/partial/atom-sitename--Configured.html"
				]
			],
			"themeviz_css" => null
		];

		$this->mockTwig->assertMethodCalledWith(
			"renderFile",
			"styleGuide.twig",
			$data
		);
	}

	public function testRendersCss()
	{
		$this->mockLess->setReturnValue("getCss", "compiled_css");

		$this->styleGuide->compile();

		$data = [
			"themeviz_components" => [],
			"themeviz_css" => "compiled_css"
		];

		$this->mockTwig->assertMethodCalledWith(
			"renderFile",
			"styleGuide.twig",
			$data
		);
	}

	public function testParsesLess()
	{
		$this->styleGuide->compile();

		$basePath = THEMEVIZ_BASE_PATH . "/style";

		$this->mockLess->assertMethodCalledWith(
			"parseFile",
			"$basePath/styleGuide.less",
			$basePath
		);
	}
}