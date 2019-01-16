<?php

namespace ThemeViz;

use ThemeViz\File\TwigFile\StyleGuide;
use ThemeViz\File\TwigFile\Summary;

class App
{
	/** @var BuildFactory $buildFactory */
	private $buildFactory;

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

    public function __construct(
    	BuildFactory $buildFactory,
		Differ $differ,
		Filesystem $filesystem,
		Git $git,
		StyleGuide $page_styleGuide,
		Summary $page_summary
    )
    {
    	$this->buildFactory = $buildFactory;
        $this->differ = $differ;
        $this->filesystem = $filesystem;
        $this->git = $git;
		$this->page_styleGuide = $page_styleGuide;
		$this->page_summary = $page_summary;
    }

    /**
     * @throws \Exception
     */
    public function compile()
    {
        $this->filesystem->deleteTree(THEMEVIZ_BASE_PATH . "/build");
		$this->runBuild("head");
		$this->page_styleGuide->save();
		$this->buildProduction();
        $this->differ->buildDiffs();
        $this->page_summary->save();
    }

	public function buildStyleGuide()
	{
		$this->runBuild("head");
		$this->page_styleGuide->save();
	}

	public function buildProduction()
	{
		$this->git->saveState(THEMEVIZ_THEME_PATH);
		$this->git->checkoutRemoteBranch(THEMEVIZ_THEME_PATH, "production");
		$this->git->pull(THEMEVIZ_THEME_PATH, "production");
		$this->runBuild("production");
		$this->git->resetState(THEMEVIZ_THEME_PATH);
	}

	/**
	 * @param $buildName
	 */
    private function runBuild($buildName): void
    {
		$build = $this->buildFactory->makeBuild($buildName);
		$build->run();
    }
}