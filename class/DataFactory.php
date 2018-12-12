<?php

namespace ThemeViz;


class DataFactory
{
	/** @var Filesystem $filesystem */
	private $filesystem;

	private $themeConfig;

	public function __construct(Filesystem $filesystem)
	{
		$this->filesystem = $filesystem;
	}

	public function makeData($array) {
		$collectionData = $this->getCollectionData();

		return new Data($array, $collectionData);
	}

	/**
	 * @return array
	 */
	private function getCollectionData(): array
	{
		$collections = $this->getCollections();

		return array_map(function ($collection) {
			return $collection["data"];
		}, $collections);
	}

	private function getCollections()
	{
		$this->themeConfig = $this->getThemeConfig();

		return $this->themeConfig["depends"]["collections"] ?? [];
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	private function getThemeConfig(): array
	{
		return $this->getCachedDecodedJsonFile(
			"themeConfig",
			THEMEVIZ_THEME_PATH . "/theme.conf"
		);
	}

	/**
	 * @param $fieldName
	 * @param $path
	 * @return mixed
	 * @throws \Exception
	 */
	private function getCachedDecodedJsonFile($fieldName, $path)
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