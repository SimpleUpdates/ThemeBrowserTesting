<?php

namespace ThemeViz;

class Component
{
	private $componentsFile;
	private $css;
	private $path;
	private $scenarios;
	private $themeConfig;

	public function __construct($componentsFile, $css, $path, $scenarios, $themeConfig)
	{
		$this->componentsFile = $componentsFile;
		$this->css = $css;
		$this->path = $path;
		$this->scenarios = $scenarios;
		$this->themeConfig = $themeConfig;
	}

	/**
	 * @return mixed
	 */
	public function getScenarios()
	{
		$defaultScenario = [];
		$scenarios = $this->scenarios ?: [$defaultScenario];

		return array_map(function ($scenario) {
			return $this->getScenarioData($scenario);
		}, $scenarios);
	}

	/**
	 * @param $scenario
	 * @return array
	 */
	private function getScenarioData($scenario)
	{
		return array_merge_recursive(
			$this->getBaseData(),
			$scenario
		);
	}

	/**
	 * @return array
	 */
	private function getBaseData(): array
	{
		$themeDefaults = $this->componentsFile["defaults"]["twig"] ?? [];

		$componentData = [
			"themeviz_component_path" => $this->getPath(),
			"themeviz_wrapper_classes" => $this->getWrapperClasses(),
			"themeviz_css" => $this->css,
			"themeviz_use_bootstrap" => $this->shouldUseBootstrap()
		];

		return array_merge($componentData, $themeDefaults);
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @return string
	 */
	private function getWrapperClasses(): string
	{
		return implode(",", $this->componentsFile["wrapperClasses"] ?? []);
	}

	/**
	 * @return bool
	 */
	private function shouldUseBootstrap(): bool
	{
		return $this->themeConfig["depends"]["settings"]["global_bootstrap"] ?? FALSE;
	}
}