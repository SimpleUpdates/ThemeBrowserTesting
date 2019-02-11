<?php

namespace ThemeViz\File\ConfigFile\ThemeConf;

class ConfigProperty
{
	protected $key;
	protected $rawProperty;

	public function __construct($key, $rawProperty)
	{
		$this->key = $key;
		$this->rawProperty = $rawProperty;
	}

	public function toLess()
	{
		$value = $this->rawProperty['value'];
		$formattedValue = $this->formatValue($value);

		return "@config-$this->key: $formattedValue;";
	}

	protected function formatValue($value)
	{
		return $value;
	}
}