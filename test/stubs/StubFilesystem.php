<?php

namespace ThemeViz;

class StubFilesystem extends Filesystem
{
    use Stub;

    public function getFile($path)
    {
		return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function deleteTree($dir)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function fileForceContents($path, $contents)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function makeTree($path)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function scanDir($path)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function isDir($path)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

	/**
	 * @param $themeConf
	 */
	public function loadThemeConf($themeConf): void
	{
		$themeConfJson = json_encode($themeConf);

		$this->setMappedReturnValues("getFile", [
			[THEMEVIZ_THEME_PATH . "/theme.conf", $themeConfJson]
		]);
	}

	public function loadComponentsFile($componentsFile): void
	{
		$componentsFileJson = json_encode($componentsFile);

		$this->setMappedReturnValues("getFile", [
			[THEMEVIZ_THEME_PATH . "/components.json", $componentsFileJson]
		]);
	}

	public function loadComponentsFileFromFilesystem($filename): void
	{
		$this->setMappedReturnValues("getFile", [
			[THEMEVIZ_THEME_PATH . "/components.json", file_get_contents(__DIR__ . "/../{$filename}")]
		]);
	}
}