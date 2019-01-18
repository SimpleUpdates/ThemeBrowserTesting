<?php

final class TestComponent extends ThemeViz\TestCase
{
	/** @var \ThemeViz\File\TwigFile\Component $component */
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
}