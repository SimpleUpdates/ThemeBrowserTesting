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
        $this->filesystem->deleteTree("build");
		$this->runBuild("head");
		$this->buildProduction();
        $this->differ->buildDiffs();
        $this->summary->save();
		$this->styleGuide->save();
    }

	public function buildStyleGuide()
	{
		$this->runBuild("head");
		$this->styleGuide->save();
	}

	public function buildProduction()
	{
		$this->cloneProductionBranch();
		$this->runBuild("production");
	}

	private function cloneProductionBranch(): void
	{
		$this->filesystem->deleteFolder("tmp");
		$this->filesystem->makeDir(THEMEVIZ_BASE_PATH . "/tmp");
		$remoteUrl = $this->git->getRemoteUrl(THEMEVIZ_THEME_PATH);
		$this->git->clone($remoteUrl, THEMEVIZ_BASE_PATH . "/tmp");
	}

	/**
	 * @param $buildName
	 */
	private function runBuild($buildName): void
	{
		$this->buildFactory->makeBuild($buildName)->run();
	}
}