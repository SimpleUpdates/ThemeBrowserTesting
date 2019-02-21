<?php

namespace ThemeViz;

use ThemeViz\File\CssAnalysis;
use ThemeViz\File\StyleSheet;

class Build {
	/** @var ComponentFactory $componentFactory */
	private $componentFactory;

	/** @var CssAnalysis $cssAnalysis */
	private $cssAnalysis;

	/** @var Filesystem $filesystem */
	private $filesystem;

	/** @var Photographer $photographer */
	private $photographer;

	/** @var StyleSheet $styleSheet */
	private $styleSheet;

	private $name;

	public function __construct(
		ComponentFactory $componentFactory,
		CssAnalysis $cssAnalysis,
		Filesystem $filesystem,
		Photographer $photographer,
		StyleSheet $styleSheet
	)
	{
		$this->componentFactory = $componentFactory;
		$this->cssAnalysis = $cssAnalysis;
		$this->filesystem = $filesystem;
		$this->photographer = $photographer;
		$this->styleSheet = $styleSheet;
	}

	public function setName(string $name)
	{
		$this->name = $name;
	}

	public function run()
	{
		$this->filesystem->deleteTree("build/$this->name");
		$this->saveStylesheet();
		$this->compileScenarios();
		$this->saveCssAnalysis();
	}

	protected function saveStylesheet(): void
	{
		$this->styleSheet->setOutPath("build/$this->name/theme.css");
		$this->styleSheet->save();
	}

	protected function compileScenarios(): void
	{
		$components = $this->componentFactory->getComponents();

		array_walk($components, function (Component $component) {
			$component->saveScenariosToDisk($this->name);
		});
	}

	protected function saveCssAnalysis(): void
	{
		$this->cssAnalysis->setBuildName($this->name);
		$this->cssAnalysis->save();
	}
}