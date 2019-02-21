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
	 * @throws \Exception
	 */
	public function getComponents()
	{
		$rawComponents = $this->componentsFile->getRawComponents();

		return array_map(function($rawComponent) {
			/** @var Component $component */
			$component = $this->factory->make("Component");

			$component->setSourcePath($rawComponent["path"]);
			$component->setScenarios($rawComponent["scenarios"]);

			return $component;
		}, $rawComponents);
	}
}