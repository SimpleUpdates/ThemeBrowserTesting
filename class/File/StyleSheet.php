<?php

namespace ThemeViz\File;


use ThemeViz\File;

class StyleSheet extends File
{
	private $outPath;

	public function setOutPath($outPath)
	{
		$this->outPath = $outPath;
	}

	protected function makeContents()
	{
		// TODO: Implement makeContents() method.
	}

	protected function getOutPath()
	{
		return $this->outPath;
	}
}