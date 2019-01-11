<?php

namespace ThemeViz;

use ThemeViz\File\TwigFile\StyleGuide;
use ThemeViz\File\TwigFile\Summary;

class App
{
    /** @var Differ $differ */
    private $differ;

    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Git $git */
    private $git;

    /** @var StyleGuide $page_styleGuide */
    private $page_styleGuide;

	/** @var Summary $page_summary */
	private $page_summary;

    /** @var Photographer $photographer */
    private $photographer;

    /** @var TwigStore $scenarioStorage */
    private $scenarioStorage;

    /** @var TwigCompiler $twigCompiler */
    private $twigCompiler;

    public function __construct(
		Differ $differ,
		Filesystem $filesystem,
		Git $git,
		StyleGuide $page_styleGuide,
		Summary $page_summary,
		Photographer $photographer,
		TwigStore $scenarioStorage,
		TwigCompiler $twigCompiler
    )
    {
        $this->differ = $differ;
        $this->filesystem = $filesystem;
        $this->git = $git;
		$this->page_styleGuide = $page_styleGuide;
		$this->page_summary = $page_summary;
        $this->photographer = $photographer;
        $this->scenarioStorage = $scenarioStorage;
        $this->twigCompiler = $twigCompiler;
    }

    /**
     * @throws \Exception
     */
    public function compile()
    {
        $this->filesystem->deleteTree(THEMEVIZ_BASE_PATH . "/build");
        $this->buildHead();
		$this->page_styleGuide->compile();
		$this->buildProduction();
        $this->differ->buildDiffs();
        $this->page_summary->compile();
    }

	public function buildStyleGuide()
	{
		$this->buildHead();
		$this->page_styleGuide->compile();
	}

	public function buildHead()
	{
		$this->makeBuild("head");
	}

	public function buildProduction()
	{
		$this->git->saveState(THEMEVIZ_THEME_PATH);
		$this->git->checkoutRemoteBranch(THEMEVIZ_THEME_PATH, "production");
		$this->git->pull(THEMEVIZ_THEME_PATH, "production");

		$this->makeBuild("production");

		$this->git->resetState(THEMEVIZ_THEME_PATH);
	}

	/**
	 * @param $buildName
	 * @throws \Less_Exception_Parser
	 */
    private function makeBuild($buildName): void
    {
		$buildPath = THEMEVIZ_BASE_PATH . "/build/$buildName";

		$this->filesystem->deleteTree($buildPath);

        if (!$components = $this->twigCompiler->compileTwig()) return;

		$this->scenarioStorage->persistTwigComponents(
			$components,
			"$buildPath/html"
		);

        $this->photographer->photographComponents(
        	"$buildPath/html",
			"$buildPath/shots"
		);
    }
}