<?php

namespace ThemeViz;

class ComponentFactory
{
	/** @var Factory $factory */
	private $factory;

	/** @var Filesystem $filesystem */
	private $filesystem;

	private $themeConfig;
	private $componentsFile;

	public function __construct(Factory $factory, Filesystem $filesystem)
	{
		$this->factory = $factory;
		$this->filesystem = $filesystem;
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

		$screens = $this->componentsFile["screens"] ?? [];

		return array_map(function($screen) {
			/** @var Component $component */
			$component = $this->factory->make("Component");

			$component->setSourcePath($screen["path"]);
			$component->setScenarios($screen["scenarios"]);

			return $component;
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