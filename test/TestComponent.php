<?php

final class TestComponent extends ThemeViz\TestCase
{
	/** @var \ThemeViz\Component $component */
	private $component;

	/**
	 * @throws ReflectionException
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->component = $this->factory->make("Component");
	}

	public function testExists()
	{
		$this->assertInstanceOf("\\ThemeViz\\Component", $this->component);
	}

	public function testGetScenarios()
	{
		$this->component->setScenarios([[]]);
		$scenarios = $this->component->getScenarios();

		$this->assertInstanceOf("\\ThemeViz\\File\\TwigFile\\Scenario", $scenarios[0]);
	}

	public function testGetName()
	{
		$this->component->setSourcePath("path/to/component");

		$name = $this->component->getName();

		$this->assertEquals("path/to/component", $name);
	}
}