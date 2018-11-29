<?php

final class TestDiffer extends ThemeViz\TestCase
{
    /** @var \ThemeViz\Differ $differ */
    private $differ;

    protected function setUp()
    {
        parent::setUp();

        $this->differ = $this->factory->getDiffer();
    }

    public function testCreatesDiffsFolder()
    {
        $this->differ->buildDiffs();

        $this->mockFilesystem->assertMethodCalledWith(
            "makeTree",
            THEMEVIZ_BASE_PATH . "/build/diffs"
        );
    }

    public function testGetsProductionShots()
    {
        $this->differ->buildDiffs();

        $this->mockFilesystem->assertMethodCalledWith(
            "scanDir",
            THEMEVIZ_BASE_PATH . "/build/production/shots"
         );
    }

    public function testGetsPullShots()
    {
        $this->differ->buildDiffs();

        $this->mockFilesystem->assertMethodCalledWith(
            "scanDir",
            THEMEVIZ_BASE_PATH . "/build/pull/shots"
        );
    }

    public function testGeneratesDiffs()
    {
        $this->mockFilesystem->setReturnValue("scanDir", ["component.png"]);

        $this->differ->buildDiffs();

        $this->mockPixelmatch->assertMethodCalledWith(
            "makeDiff",
            THEMEVIZ_BASE_PATH . "/build/production/shots/component.png",
            THEMEVIZ_BASE_PATH . "/build/pull/shots/component.png",
            THEMEVIZ_BASE_PATH . "/build/diffs/component.png"
        );
    }

    public function testOnlyMakesDiffsForComponentsInBothBranches()
    {
        $this->mockFilesystem->setReturnValueAt(0, "scanDir",
            ["component.png", "another.png"]);
        $this->mockFilesystem->setReturnValueAt(1, "scanDir",
            ["component.png"]);

        $this->differ->buildDiffs();

        $this->mockPixelmatch->assertCallCount("makeDiff", 1);
    }

    public function testDoesNotDiffDots()
    {
        $this->mockFilesystem->setReturnValue("scanDir", [".",".."]);

        $this->differ->buildDiffs();

        $this->mockPixelmatch->assertMethodNotCalled("makeDiff");
    }
}