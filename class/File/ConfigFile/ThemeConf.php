<?php

namespace ThemeViz\File\ConfigFile;


use ThemeViz\File\ConfigFile;

class ThemeConf extends ConfigFile
{
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