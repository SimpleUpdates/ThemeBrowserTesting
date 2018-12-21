<?php

final class TestStyleGuide extends ThemeViz\TestCase
{
	/** @var \ThemeViz\Page\StyleGuide $styleGuide */
	private $styleGuide;

	protected function setUp()
	{
		parent::setUp();

		$this->styleGuide = $this->factory->getPage_StyleGuide();
	}

	public function testExists()
	{
		$this->assertInstanceOf("\\ThemeViz\\Page\\StyleGuide", $this->styleGuide);
	}

	public function testExtendsPage()
	{
		$this->assertInstanceOf("\\ThemeViz\\Page", $this->styleGuide);
	}

	public function testPassesComponentsToTemplate()
	{
		$this->mockFilesystem->setReturnValue("findPathsMatchingRecursive", ["component_file"]);

		$this->styleGuide->compile();

		$data = ["themeviz_components" => [
			[
				"name" => "component_file",
				"html" => "component_file"
			]
		]];

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

	public function testSimplifiesComponentName()
	{
		$path = "/Users/work/ProgrammingProjects/ThemeViz/build/head/html/partial/atom-sitename--Configured.html";

		$this->mockFilesystem->setReturnValue(
			"findPathsMatchingRecursive",
			[$path]
		);

		$this->styleGuide->compile();

		$data = ["themeviz_components" => [
			[
				"name" => "atom-sitename--Configured.html",
				"html" => $path
			]
		]];

		$this->mockTwig->assertMethodCalledWith(
			"renderFile",
			"styleGuide.twig",
			$data
		);
	}
}
