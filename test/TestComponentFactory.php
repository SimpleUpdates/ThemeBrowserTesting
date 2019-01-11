<?php

final class TestComponentFactory extends ThemeViz\TestCase
{
	/** @var \ThemeViz\ComponentFactory $componentRepository */
	private $componentRepository;

	protected function setUp()
	{
		parent::setUp();

		$this->componentRepository = $this->factory->get("ComponentFactory");
	}

	public function testExists()
	{
		$this->assertInstanceOf("\\ThemeViz\\ComponentFactory", $this->componentRepository);
	}

	public function testBuildComponent()
	{
		$this->loadMinimalComponentsFile();

		$components = $this->componentRepository->getComponents();

		$this->assertInstanceOf("\\ThemeViz\\Component", $components[0]);
	}
}