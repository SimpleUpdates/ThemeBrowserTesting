<?php

namespace ThemeViz;


class ComponentFactory
{
	/** @var Filesystem $filesystem */
	private $filesystem;

	/** @var LessCompiler $lessCompiler */
	private $lessCompiler;

	private $css;

	private $themeConfig;
	private $componentsFile;

	public function __construct(Filesystem $filesystem, LessCompiler $lessCompiler)
	{
		$this->filesystem = $filesystem;
		$this->lessCompiler = $lessCompiler;
	}

	/**
	 * @return array
	 * @throws \Less_Exception_Parser
	 * @throws \Exception
	 */
	public function getComponents()
	{
		$this->themeConfig = $this->getThemeConfig();
		$this->componentsFile = $this->getComponentsFile();

		$this->css = $this->css ?? $this->lessCompiler->getCss($this->themeConfig, $this->componentsFile);

		$screens = $this->componentsFile["screens"] ?? [];

		return array_map(function($screen) {
			return new Component($this->componentsFile, $this->css, $screen, $this->themeConfig);
		}, $screens);
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	private function getComponentsFile(): array
	{
		return $this->getCachedDecodedJsonFile(
			"componentsFile",
			THEMEVIZ_THEME_PATH . "/components.json"
		);
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	private function getThemeConfig(): array
	{
		return $this->getCachedDecodedJsonFile(
			"themeConfig",
			THEMEVIZ_THEME_PATH . "/theme.conf"
		);
	}

	/**
	 * @param $fieldName
	 * @param $path
	 * @return mixed
	 * @throws \Exception
	 */
	private function getCachedDecodedJsonFile($fieldName, $path)
	{
		if (!$this->$fieldName) {
			$json = $this->filesystem->getFile($path);

			$this->$fieldName = json_decode($json, TRUE);

			if ($json && $this->$fieldName === NULL) {
				throw new \Exception("Error attempting to decode json file: $path");
			}
		}

		return $this->$fieldName;
	}
}