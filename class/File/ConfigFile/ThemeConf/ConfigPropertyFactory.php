<?php

namespace ThemeViz\File\ConfigFile\ThemeConf;

class ConfigPropertyFactory
{
	private $classMap = [
		"image" => "ConfigProperty\\Image",
		"text" => "ConfigProperty\\Text"
	];

	public function getProperties($rawProperties)
	{
		return array_map(function($key) use($rawProperties) {
			return $this->getProperty($key, $rawProperties[$key]);
		}, array_keys($rawProperties));
	}

	public function getProperty($key, $rawProperty) {
		$class = $this->classMap[$rawProperty["type"]] ?? "ConfigProperty";
		$fullClass = "ThemeViz\\File\\ConfigFile\\ThemeConf\\$class";

		return new $fullClass($key, $rawProperty);
	}
}