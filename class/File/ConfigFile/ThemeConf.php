<?php

namespace ThemeViz\File\ConfigFile;

use ThemeViz\File\ConfigFile;
use ThemeViz\File\ConfigFile\ThemeConf\ConfigProperty;
use ThemeViz\File\ConfigFile\ThemeConf\ConfigPropertyFactory;
use ThemeViz\Filesystem;

class ThemeConf extends ConfigFile
{
	/** @var ConfigPropertyFactory $configPropertyFactory */
	private $configPropertyFactory;

	public function __construct(ConfigPropertyFactory $configPropertyFactory, Filesystem $filesystem)
	{
		parent::__construct($filesystem);

		$this->configPropertyFactory = $configPropertyFactory;
	}

	public function getLess()
	{
		$rawProperties = $this->getContents()["config"] ?? [];
		$properties = $this->configPropertyFactory->getProperties($rawProperties);

		return array_reduce($properties, function($carry, ConfigProperty $property) {
			return $carry . $property->toLess();
		}, "");
	}

	public function getContents()
	{
		return $this->getCachedDecodedJsonFile(
			"themeConfig",
			THEMEVIZ_THEME_PATH . "/theme.conf"
		);
	}

	protected function makeContents()
	{
		// TODO: Implement makeContents() method.
	}

	protected function getOutPath()
	{
		// TODO: Implement getOutPath() method.
	}
}