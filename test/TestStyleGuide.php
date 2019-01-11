<?php

final class TestStyleGuide extends ThemeViz\TestCase
{
	/** @var \ThemeViz\File\TwigFile\StyleGuide $styleGuide */
	private $styleGuide;

	protected function setUp()
	{
		parent::setUp();

		$this->styleGuide = $this->factory->getFile_TwigFile_StyleGuide();
	}

	public function testExists()
	{
		$this->assertInstanceOf("\\ThemeViz\\File\\TwigFile\\StyleGuide", $this->styleGuide);
	}

	public function testExtendsPage()
	{
		$this->assertInstanceOf("\\ThemeViz\\File\\TwigFile", $this->styleGuide);
	}

	public function testPassesComponentsToTemplate()
	{
		$this->mockFilesystem->setReturnValue("findPathsMatchingRecursive", ["/path/to/build/file"]);

		$this->styleGuide->save();

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
		$this->styleGuide->save();

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

		$this->styleGuide->save();

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

		$this->styleGuide->save();

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
		$this->styleGuide->save();

		$basePath = THEMEVIZ_BASE_PATH . "/style";

		$this->mockLess->assertMethodCalledWith(
			"parseFile",
			"$basePath/styleGuide.less",
			$basePath
		);
	}
}
