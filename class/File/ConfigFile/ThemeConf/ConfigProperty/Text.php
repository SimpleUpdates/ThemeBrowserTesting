<?php

namespace ThemeViz\File\ConfigFile\ThemeConf\ConfigProperty;

use ThemeViz\File\ConfigFile\ThemeConf\ConfigProperty;

class Text extends ConfigProperty
{
	protected function formatValue($value)
	{
		return "\"$value\"";
	}
}