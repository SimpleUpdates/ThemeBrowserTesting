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

    /** @var StyleGuide $styleGuide */
    private $styleGuide;

	/** @var Summary $summary */
	private $summary;

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
		$this->styleGuide = $page_styleGuide;
		$this->summary = $page_summary;
    }

    /**
     * @throws \Exception
     */
    public function compile()
    {
        $this->filesystem->deleteTree(THEMEVIZ_BASE_PATH . "/build");
		$this->runBuild("head");
		$this->styleGuide->save();
		$this->buildProduction();
        $this->differ->buildDiffs();
        $this->summary->save();
    }

	public function buildStyleGuide()
	{
		$this->runBuild("head");
		$this->styleGuide->save();
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
		$this->buildFactory->makeBuild($buildName)->run();
    }
}