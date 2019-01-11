<?php

final class TestSummary extends ThemeViz\TestCase
{
	/** @var \ThemeViz\File\TwigFile\Summary $summary */
	private $summary;

	protected function setUp()
	{
		parent::setUp();

		$this->summary = $this->factory->get("File\\TwigFile\\Summary");
	}

	public function testRendersSummary()
	{
		$this->summary->save();

		$this->mockTwig->assertMethodCalled("renderFile");
	}

	public function testScansDiffs()
	{
		$this->summary->save();

		$this->mockFilesystem->assertMethodCalledWith(
			"scanDir",
			THEMEVIZ_BASE_PATH . "/build/diffs"
		);
	}

	public function testPassesComponentsToTemplate()
	{
		$this->mockFilesystem->setReturnValue("scanDir", ["component.png"]);

		$this->summary->save();

		$data = [
			"themeviz_components" => [
				[
					"name" => "component.png",
					"expected" => "production/shots/component.png",
					"actual" => "head/shots/component.png",
					"diff" => "diffs/component.png"
				]
			]
		];

		$this->mockTwig->assertMethodCalledWith(
			"renderFile",
			"summary.twig",
			$data
		);
	}

	public function testWritesOutSummaryFile()
	{
		$this->mockTwig->setReturnValue("renderFile", "rendered_twig");

		$this->summary->save();

		$this->mockFilesystem->assertMethodCalledWith(
			"fileForceContents",
			THEMEVIZ_BASE_PATH . "/build/summary.html",
			"rendered_twig"
		);
	}
}