<?php

namespace ThemeViz;

class Component
{
	private $componentsFile;
	private $css;
	private $screen;
	private $themeConfig;

	public function __construct($componentsFile, $css, $screen, $themeConfig)
	{
		$this->componentsFile = $componentsFile;
		$this->css = $css;
		$this->screen = $screen;
		$this->themeConfig = $themeConfig;
	}

	/**
	 * @return mixed
	 */
	public function getScenarios()
	{
		$defaultScenario = [];
		$scenarios = $this->screen["scenarios"] ?: [$defaultScenario];

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
		$classes = implode(",", $this->componentsFile["wrapperClasses"] ?? []);
		$useBootstrap = $this->themeConfig["depends"]["settings"]["global_bootstrap"] ?? FALSE;
		$componentData = [
			"themeviz_component_path" => $this->screen["path"],
			"themeviz_wrapper_classes" => $classes,
			"themeviz_css" => $this->css,
			"themeviz_use_bootstrap" => $useBootstrap
		];
		$themeDefaults = $this->componentsFile["defaults"]["twig"] ?? [];

		return array_merge_recursive($componentData, $themeDefaults, $scenario);
	}
}