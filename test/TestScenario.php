<?php

final class TestScenario extends ThemeViz\TestCase
{
	/** @var \ThemeViz\File\TwigFile\Scenario $scenario */
	private $scenario;

	/**
	 * @throws ReflectionException
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->scenario = $this->factory->make("File\\TwigFile\\Scenario");
	}

	public function testExists()
	{
		$this->assertInstanceOf("\\ThemeViz\\File\\TwigFile\\Scenario", $this->scenario);
	}

	public function testIncludesStyleSheetPath()
	{
		$this->scenario->setBuildName("head");
		$this->scenario->setScenarioData([
			"themeviz_component_path" => "partial/atom-sitename.html"
		]);
		$this->scenario->save();

		$this->mockTwig->assertTwigTemplateRenderedWithData("component.twig", [
			"themeviz_css_href" => "../../theme.css"
		]);
	}
}