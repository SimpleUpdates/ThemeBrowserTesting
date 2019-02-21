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

		$this->mockTwig->assertTwigTemplateRendered("component.twig", [
			"themeviz_css_href" => "../../theme.css"
		], "head");
	}

	public function testGetHeadHtml()
	{
		$this->scenario->setScenarioData([
			"themeviz_component_path" => "partial/atom-sitename.html"
		]);

		$this->scenario->getHeadHtml();

		$this->mockTwig->assertTwigTemplateRendered("partial/atom-sitename.html", [
			"themeviz_css_href" => "../../theme.css"
		], "head");
	}

	public function testGetProductionHtml()
	{
		$this->scenario->setScenarioData([
			"themeviz_component_path" => "partial/atom-sitename.html"
		]);

		$this->scenario->getProductionHtml();

		$this->mockTwig->assertTwigTemplateRendered("partial/atom-sitename.html", [
			"themeviz_css_href" => "../../theme.css"
		], "production");
	}

	public function testGetHeadHtmlReturnsHtml()
	{
		$this->mockTwig->setReturnValue("renderFile", "html");

		$result = $this->scenario->getHeadHtml();

		$this->assertEquals("html", $result);
	}

	public function testGetProductionHtmlReturnsHtml()
	{
		$this->mockTwig->setReturnValue("renderFile", "html");

		$result = $this->scenario->getProductionHtml();

		$this->assertEquals("html", $result);
	}

	public function testGetName()
	{
		$this->scenario->setName("name");

		$name = $this->scenario->getName();

		$this->assertEquals("name", $name);
	}

	public function testGetBuildPath()
	{
		$this->scenario->setName("name")->setBuildName("buildName")->setScenarioData([
			"themeviz_component_path" => "partial/atom-sitename.html"
		]);

		$name = $this->scenario->getRelativePath();

		$this->assertEquals('partial/atom-sitename--name.html', $name);
	}
}