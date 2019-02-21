<?php

namespace ThemeViz\File\ConfigFile;


use ThemeViz\File\ConfigFile;

class ComponentsFile extends ConfigFile
{
	public function getRawComponents()
	{
		return $this->getContents()["screens"] ?? [];
	}

	public function getContents()
	{
		return $this->getCachedDecodedJsonFile(
			"componentsFile",
			THEMEVIZ_THEME_PATH . "/components.json"
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