<?php

final class TestStyleSheet extends ThemeViz\TestCase
{
	/** @var \ThemeViz\File\StyleSheet $styleSheet */
	private $styleSheet;

	protected function setUp()
	{
		parent::setUp();

		$this->styleSheet = $this->factory->get("File\\StyleSheet");
	}

	public function testExists()
	{
		$this->assertInstanceOf("\\ThemeViz\\File\\StyleSheet", $this->styleSheet);
	}

	public function testSetOutPath()
	{
		$this->styleSheet->setOutPath("provided/out/path");

		$this->styleSheet->save();

		$this->mockFilesystem->assertAnyCallMatches("fileForceContents", function($carry, $call) {
			$outPath = $call[0];

			return $carry || $outPath === THEMEVIZ_BASE_PATH . "/provided/out/path";
		});
	}
}