<?php

namespace ThemeViz\File;


use ThemeViz\File;
use ThemeViz\File\ConfigFile\ComponentsFile;
use ThemeViz\File\ConfigFile\ThemeConf;
use ThemeViz\Filesystem;
use ThemeViz\Less;
use ThemeViz\LessCompiler;

class StyleSheet extends File
{
	/** @var ComponentsFile $componentsFile */
	private $componentsFile;

	/** @var ThemeConf $themeConf */
	private $themeConf;

	/** @var Less $less */
	private $less;

	private $outPath;

	public function __construct(
		ComponentsFile $componentsFile,
		Filesystem $filesystem,
		Less $less,
		ThemeConf $themeConf
	)
	{
		parent::__construct($filesystem);

		$this->componentsFile = $componentsFile;
		$this->less = $less;
		$this->themeConf = $themeConf;
	}

	public function setOutPath($outPath)
	{
		$this->outPath = $outPath;
	}

	protected function makeContents()
	{
		$componentsFile = $this->componentsFile->getContents();

		return $this->getCss($componentsFile);
	}

	protected function getOutPath()
	{
		return $this->outPath;
	}

	/**
	 * @param $componentsFile
	 * @return string
	 * @throws \Exception
	 */
	private function getCss($componentsFile)
	{
		$this->less->resetParser();

		$this->parseBaseLess();
		$this->less->parse("@su-assetpath: \"".THEMEVIZ_THEME_PATH."/asset\";");
		$this->less->parse($this->themeConf->getLess());
		$this->parseLessDefaults($componentsFile);
		$this->parseThemeGlobalLess();

		return $this->less->getCss();
	}

	private function parseLessDefaults($componentsFile): void
	{
		$defaults = $componentsFile["defaults"]["less"] ?? [];

		$defaultsString = array_reduce(array_keys($defaults), function ($carry, $key) use ($defaults) {
			$varName = $key;
			$varValue = $defaults[$key];

			return "$carry $varName: $varValue;";
		}, "");

		$this->less->parse($defaultsString);
	}

	private function parseBaseLess(): void
	{
		$baseLess = $this->filesystem->getFile(THEMEVIZ_BASE_PATH . "/view/base.less") ?? "";

		$this->less->parse($baseLess);
	}

	private function parseThemeGlobalLess(): void
	{
		$this->less->parseFile(THEMEVIZ_THEME_PATH . "/style/global.less", THEMEVIZ_THEME_PATH);
	}
}