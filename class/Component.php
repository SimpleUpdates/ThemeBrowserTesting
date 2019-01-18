<?php

namespace ThemeViz;

use ThemeViz\File\ConfigFile\ComponentsFile;
use ThemeViz\File\ConfigFile\ThemeConf;
use ThemeViz\File\TwigFile\Scenario;

class Component
{
	/** @var ComponentsFile $componentsFile */
	private $componentsFile;

	/** @var DataFactory $dataFactory */
	private $dataFactory;

	/** @var Factory $factory */
	private $factory;

	/** @var ThemeConf $themeConfig */
	private $themeConfig;

	/** @var Twig $twig */
	private $twig;

	protected $template = "component.twig";

	private $css;
	private $sourcePath;
	private $scenarios;

	public function __construct(
		ComponentsFile $componentsFile,
		DataFactory $dataFactory,
		Factory $factory,
		ThemeConf $themeConf,
		Twig $twig
	)
	{
		$this->componentsFile = $componentsFile;
		$this->dataFactory = $dataFactory;
		$this->factory = $factory;
		$this->themeConfig = $themeConf;
		$this->twig = $twig;
	}

	protected function getDataArray()
	{
		return [];
	}

	/**
	 * @param mixed $path
	 * @return Component
	 */
	public function setSourcePath($path)
	{
		$this->sourcePath = $path;
		return $this;
	}

	/**
	 * @param mixed $scenarios
	 * @return Component
	 */
	public function setScenarios($scenarios)
	{
		$this->scenarios = $scenarios;
		return $this;
	}

	public function compileScenarios($buildName)
	{
		$scenarios = $this->getScenarios();

		return array_walk(array_keys($scenarios), function ($key) use($scenarios, $buildName) {
			$scenarioName = $key;
			$scenario = $scenarios[$key];

			$data = $this->dataFactory->makeData($scenario);

			/** @var Scenario $scenario */
			$scenario = $this->factory->make("File\\TwigFile\\Scenario");
			$scenario->setName($scenarioName);
			$scenario->setScenarioData($data);
			$scenario->setBuildName($buildName);
			$scenario->save();
		});
	}

	/**
	 * @return mixed
	 */
	private function getScenarios()
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
		$themeDefaults = $this->componentsFile->getContents()["defaults"]["twig"] ?? [];

		$componentData = [
			"themeviz_component_path" => $this->sourcePath,
			"themeviz_wrapper_classes" => $this->getWrapperClasses(),
			"themeviz_css" => $this->css,
			"themeviz_use_bootstrap" => $this->shouldUseBootstrap()
		];

		return array_merge($componentData, $themeDefaults);
	}

	/**
	 * @return string
	 */
	private function getWrapperClasses(): string
	{
		return implode(",", $this->componentsFile->getContents()["wrapperClasses"] ?? []);
	}

	/**
	 * @return bool
	 */
	private function shouldUseBootstrap(): bool
	{
		return $this->themeConfig->getContents()["depends"]["settings"]["global_bootstrap"] ?? FALSE;
	}
}