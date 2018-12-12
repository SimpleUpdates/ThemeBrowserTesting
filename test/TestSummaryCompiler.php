<?php

final class TestSummaryCompiler extends ThemeViz\TestCase
{
    /** @var \ThemeViz\SummaryCompiler $summaryCompiler */
    private $summaryCompiler;

    protected function setUp()
    {
        parent::setUp();

        $this->summaryCompiler = $this->factory->getSummaryCompiler();
    }

    public function testRendersSummary()
    {
        $this->summaryCompiler->compile();

        $this->mockTwig->assertMethodCalled("renderFile");
    }

    public function testScansDiffs()
    {
        $this->summaryCompiler->compile();

        $this->mockFilesystem->assertMethodCalledWith(
            "scanDir",
            THEMEVIZ_BASE_PATH . "/build/diffs"
        );
    }

    public function testPassesComponentsToTemplate()
    {
        $this->mockFilesystem->setReturnValue("scanDir", ["component.png"]);

        $this->summaryCompiler->compile();

        $data = new ThemeViz\Data(["themeviz_components" => [
			[
				"name" => "component.png",
				"expected" => "production/shots/component.png",
				"actual" => "pull/shots/component.png",
				"diff" => "diffs/component.png"
			]
		]]);

        $this->mockTwig->assertMethodCalledWith(
            "renderFile",
            "summary.twig",
            $data
        );
    }

    public function testWritesOutSummaryFile()
    {
        $this->mockTwig->setReturnValue("renderFile", "rendered_twig");

        $this->summaryCompiler->compile();

        $this->mockFilesystem->assertMethodCalledWith(
            "fileForceContents",
            THEMEVIZ_BASE_PATH . "/build/summary.html",
            "rendered_twig"
        );
    }
}