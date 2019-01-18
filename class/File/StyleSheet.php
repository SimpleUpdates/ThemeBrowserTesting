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
		$themeConfig = $this->themeConf->getContents();
		$componentsFile = $this->componentsFile->getContents();

		return $this->getCss($themeConfig, $componentsFile);
	}

	protected function getOutPath()
	{
		return $this->outPath;
	}

	/**
	 * @param $themeConfig
	 * @param $componentsFile
	 * @return string
	 * @throws \Exception
	 */
	private function getCss($themeConfig, $componentsFile)
	{
		$this->less->resetParser();

		$this->parseBaseLess();
		$this->less->parse("@su-assetpath: \"".THEMEVIZ_THEME_PATH."/asset\";");
		$this->parseThemeConfigProperties($themeConfig);
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

	private function parseThemeConfigProperties($themeConfig): void
	{
		$properties = $themeConfig["config"] ?? [];
		$keys = array_keys($properties);

		$themeConfigLess = array_reduce($keys, function ($carry, $key) use ($properties) {
			$property = $properties[$key];
			$formattedValue = $this->formatValue($property);

			return "$carry @config-$key: $formattedValue;";
		}, "");

		$this->less->parse($themeConfigLess);
	}

	private function formatValue($property)
	{
		$rawValue = $property['value'];

		if ($this->isImageProperty($property)) {
			return  $this->formatImageLessValue($rawValue);
		}

		if ($property["type"] === "text") {
			return '"'.$rawValue.'"';
		}

		return $rawValue;
	}

	/**
	 * @param $value
	 * @return string
	 */
	private function formatImageLessValue($value): string
	{
		$vendorSubstitutedPathFragment = str_replace("{{ su.misc.privatelabel }}", "su", $value);
		$fullPath = THEMEVIZ_THEME_PATH . "/asset/$vendorSubstitutedPathFragment";

		return "'$fullPath'";
	}

	/**
	 * @param $property
	 * @return bool
	 */
	private function isImageProperty($property): bool
	{
		return $property["type"] === "image";
	}
}