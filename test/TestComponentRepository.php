<?php

final class TestComponentRepository extends ThemeViz\TestCase
{
	/** @var \ThemeViz\ComponentRepository $componentRepository */
	private $componentRepository;

	protected function setUp()
	{
		parent::setUp();

		$this->componentRepository = $this->factory->getComponentRepository();
	}

	public function testExists()
	{
		$this->assertInstanceOf("\\ThemeViz\\ComponentRepository", $this->componentRepository);
	}

	public function testBuildComponent()
	{
		$c = $this->componentRepository->getComponent([],[],[],[],[]);

		$this->assertInstanceOf("\\ThemeViz\\Component", $c);
	}
}