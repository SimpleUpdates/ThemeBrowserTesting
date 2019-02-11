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

		$this->mockFilesystem->assertAnyCallMatches("fileForceContents", function ($carry, $call) {
			$outPath = $call[0];

			return $carry || $outPath === THEMEVIZ_BASE_PATH . "/provided/out/path";
		});
	}

	public function testDoesNotCacheCss()
	{
		$this->mockLess->setReturnValue("getCss", "compiled_css");

		$this->styleSheet->save();
		$this->styleSheet->save();

		$this->mockLess->assertCallCount("getCss", 2);
	}

	public function testResetsParser()
	{
		$this->styleSheet->save();

		$this->mockLess->assertMethodCalled("resetParser");
	}

	public function testFormatsStrings()
	{
		$this->mockFilesystem->loadComponentsFileFromFilesystem("testComponentsFile1.json");
		$this->mockFilesystem->loadThemeConf([
			"config" => [
				"heroSlogan" => [
					"title" => "Hero Slogan",
					"type" => "text",
					"value" => "Welcome to your website's fresh look!"
				],
			]
		]);

		$this->styleSheet->save();

		$this->mockLess->assertCallsContain(
			"parse",
			"@config-heroSlogan: \"Welcome to your website's fresh look!\";"
		);
	}

	public function testDefinesAssetPathVariable()
	{
		$this->mockFilesystem->loadComponentsFileFromFilesystem("testComponentsFile1.json");

		$this->styleSheet->save();

		$this->mockLess->assertCallsContain(
			"parse",
			"@su-assetpath: \"" . THEMEVIZ_THEME_PATH . "/asset\";"
		);
	}

	public function testIncludeFocalPointX()
	{
		$this->mockFilesystem->loadComponentsFileFromFilesystem("testComponentsFile1.json");
		$this->mockFilesystem->loadThemeConf([
			"config" => [
				"headlineHomeBgImage" => [
					"title" => "Default Home - home page alternative to Carousel using the featured image option",
					"type" => "image",
					"value" => "{{ su.misc.privatelabel }}/hero_image.jpg",
					"image_min_size" => "1500 x 425"
				]
			]
		]);

		$this->styleSheet->save();

		$this->mockLess->assertCallsContain(
			"parse",
			"@config-headlineHomeBgImage-focalpoint-x: 50;"
		);
	}

	public function testIncludeFocalPointY()
	{
		$this->mockFilesystem->loadComponentsFileFromFilesystem("testComponentsFile1.json");
		$this->mockFilesystem->loadThemeConf([
			"config" => [
				"headlineHomeBgImage" => [
					"title" => "Default Home - home page alternative to Carousel using the featured image option",
					"type" => "image",
					"value" => "{{ su.misc.privatelabel }}/hero_image.jpg",
					"image_min_size" => "1500 x 425"
				]
			]
		]);

		$this->styleSheet->save();

		$this->mockLess->assertCallsContain(
			"parse",
			"@config-headlineHomeBgImage-focalpoint-y: 50;"
		);
	}
}