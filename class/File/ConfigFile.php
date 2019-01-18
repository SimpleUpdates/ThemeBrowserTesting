<?php

namespace ThemeViz\File;


use ThemeViz\File;

abstract class ConfigFile extends File
{
	/**
	 * @param $fieldName
	 * @param $path
	 * @return mixed
	 * @throws \Exception
	 */
	protected function getCachedDecodedJsonFile($fieldName, $path)
	{
		if (!$this->$fieldName) {
			$json = $this->filesystem->getFile($path);

			$this->$fieldName = json_decode($json, TRUE);

			if ($json && $this->$fieldName === NULL) {
				throw new \Exception("Error attempting to decode json file: $path");
			}
		}

		return $this->$fieldName;
	}
}
