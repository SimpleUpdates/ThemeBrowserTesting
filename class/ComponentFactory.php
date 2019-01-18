<?php

namespace ThemeViz;

use ThemeViz\File\ConfigFile\ComponentsFile;

class ComponentFactory
{
	/** @var ComponentsFile $componentsFile */
	private $componentsFile;

	/** @var Factory $factory */
	private $factory;

	/** @var Filesystem $filesystem */
	private $filesystem;

	public function __construct(ComponentsFile $componentsFile, Factory $factory, Filesystem $filesystem)
	{
		$this->componentsFile = $componentsFile;
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
		$screens = $this->componentsFile->getContents()["screens"] ?? [];

		return array_map(function($screen) {
			/** @var Component $component */
			$component = $this->factory->make("Component");

			$component->setSourcePath($screen["path"]);
			$component->setScenarios($screen["scenarios"]);

			return $component;
		}, $screens);
	}
}