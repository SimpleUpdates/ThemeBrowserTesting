<?php

final class TestStyleGuide extends ThemeViz\TestCase
{
	/** @var \ThemeViz\File\TwigFile\StyleGuide $styleGuide */
	private $styleGuide;

	protected function setUp()
	{
		parent::setUp();

		$this->styleGuide = $this->factory->get("File\\TwigFile\\StyleGuide");
	}

	public function testExists()
	{
		$this->assertInstanceOf("\\ThemeViz\\File\\TwigFile\\StyleGuide", $this->styleGuide);
	}

	public function testExtendsPage()
	{
		$this->assertInstanceOf("\\ThemeViz\\File\\TwigFile", $this->styleGuide);
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

	public function testOutputsFile()
	{
		$expected = THEMEVIZ_BASE_PATH . "/build/styleGuide.html";

		$this->styleGuide->save();

		$this->mockFilesystem->assertAnyCallMatches("fileForceContents", function ($carry, $call) use ($expected) {
			$callPath = $call[0];

			return $carry || $callPath === $expected;
		});
	}

	public function testPassesComponentObjects()
	{
		$this->mockFilesystem->loadMinimalComponentsFile();

		$this->styleGuide->save();

		$this->mockTwig->assertTwigTemplateRenderedWithDataMatching(
			"styleGuide.twig", null, function ($callData) {
				return $callData["themeviz_components"][0] instanceof \ThemeViz\Component;
			}
		);
	}
}
