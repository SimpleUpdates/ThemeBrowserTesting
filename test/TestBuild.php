<?php

final class TestBuild extends ThemeViz\TestCase
{
	/** @var \ThemeViz\Build $build */
	private $build;

	protected function setUp()
	{
		parent::setUp();

		$this->build = $this->factory->get("Build");
	}

	public function testRun()
	{
		$this->build->run();

		$this->mockFilesystem->assertMethodCalled("deleteTree");
	}

	public function testSetBuildName()
	{
		$this->build->setName("head");
		$this->build->run();

		$this->mockFilesystem->assertMethodCalledWith(
			"deleteTree",
			THEMEVIZ_BASE_PATH . "/build/head"
		);
	}

	public function testCompilesCss()
	{
		$this->build->run();

		$this->mockLess->assertMethodCalled("getCss");
	}

	public function testSetsStyleSheetOutPath()
	{
		$this->mockLess->setReturnValue("getCss", "compiled_css");

		$this->build->setName("head");
		$this->build->run();

		$this->mockFilesystem->assertMethodCalledWith(
			"fileForceContents",
			THEMEVIZ_BASE_PATH . "/build/head/theme.css",
			"compiled_css"
		);
	}
}