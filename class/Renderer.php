<?php

namespace ThemeViz;

class Renderer
{
    /** @var Differ $differ */
    private $differ;

    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Git $git */
    private $git;

    /** @var Photographer $photographer */
    private $photographer;

    /** @var ScenarioStorage $scenarioStorage */
    private $scenarioStorage;

    /** @var SummaryCompiler $summaryCompiler */
    private $summaryCompiler;

    /** @var TwigCompiler $twigCompiler */
    private $twigCompiler;

    public function __construct(
        Differ $differ,
        Filesystem $filesystem,
        Git $git,
        Photographer $photographer,
        ScenarioStorage $scenarioStorage,
        SummaryCompiler $summaryCompiler,
        TwigCompiler $twigCompiler
    )
    {
        $this->differ = $differ;
        $this->filesystem = $filesystem;
        $this->git = $git;
        $this->photographer = $photographer;
        $this->scenarioStorage = $scenarioStorage;
        $this->summaryCompiler = $summaryCompiler;
        $this->twigCompiler = $twigCompiler;
    }

    /**
     * @throws \Exception
     */
    public function compile()
    {
        $this->filesystem->deleteTree(THEMEVIZ_BASE_PATH . "/build");

        $this->makeBuild("pull");

        $this->git->saveState(THEMEVIZ_THEME_PATH);
        $this->git->checkoutRemoteBranch(THEMEVIZ_THEME_PATH, "production");
        $this->git->pull(THEMEVIZ_THEME_PATH, "production");

        $this->makeBuild("production");

        $this->git->resetState(THEMEVIZ_THEME_PATH);

        $this->differ->buildDiffs();

        $this->summaryCompiler->compile();
    }

    /**
     * @param $buildName
	 */
    private function makeBuild($buildName): void
    {
        if (!$components = $this->twigCompiler->compileTwig()) return;

        $componentFolder = THEMEVIZ_BASE_PATH . "/build/$buildName/html";
        $photoFolder = THEMEVIZ_BASE_PATH . "/build/$buildName/shots";

        $this->scenarioStorage->persistScenarios($components, $componentFolder);
        $this->photographer->photographComponents($componentFolder, $photoFolder);
    }
}