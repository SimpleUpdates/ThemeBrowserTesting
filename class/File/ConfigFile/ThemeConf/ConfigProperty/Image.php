<?php

namespace ThemeViz\File\ConfigFile\ThemeConf\ConfigProperty;

use ThemeViz\File\ConfigFile\ThemeConf\ConfigProperty;

class Image extends ConfigProperty
{
	public function toLess()
	{
		$value = $this->rawProperty['value'];
		$formattedValue = $this->formatValue($value);

		return "@config-$this->key: $formattedValue;" .
			"@config-$this->key-focalpoint-x: 50;" .
			"@config-$this->key-focalpoint-y: 50;";
	}

	protected function formatValue($value)
	{
		$vendorSubstitutedPathFragment = str_replace("{{ su.misc.privatelabel }}", "su", $value);
		$fullPath = THEMEVIZ_THEME_PATH . "/asset/$vendorSubstitutedPathFragment";

		return "'$fullPath'";
	}
}