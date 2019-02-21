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

	/**
	 * @return mixed
	 */
	public function getScenarios()
	{
		return $this->scenarios;
	}

	public function getName()
	{
		return $this->sourcePath;
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
		$keys = array_keys($scenarios ?? []);

		$this->scenarios = array_map(function($key) use ($scenarios) {
			$data = $this->prepareScenarioData($scenarios[$key]);

			/** @var Scenario $scenario */
			return $this->factory->make("File\\TwigFile\\Scenario")
				->setName($key)
				->setScenarioData($data);
		}, $keys);

		return $this;
	}

	public function saveScenariosToDisk($buildName)
	{
		$scenarios = $this->scenarios ?? [];
		return array_walk($scenarios, function ($scenario) use($buildName) {
			$scenario->setBuildName($buildName)->save();
		});
	}

	private function prepareScenarioData($scenarioData) {
		$scenarioData = $this->addBaseData($scenarioData);
		return $this->dataFactory->makeData($scenarioData);
	}

	/**
	 * @param $scenarioData
	 * @return array
	 */
	private function addBaseData($scenarioData)
	{
		return array_merge_recursive(
			$this->getBaseData(),
			$scenarioData
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