<?php

namespace ThemeViz\File;


use ThemeViz\Doiuse;
use ThemeViz\File;
use ThemeViz\Filesystem;

class CssAnalysis extends File
{
	/** @var Doiuse $doiuse */
	private $doiuse;

	private $buildName;

	public function __construct(Doiuse $doiuse, Filesystem $filesystem)
	{
		parent::__construct($filesystem);

		$this->doiuse = $doiuse;
	}

	/**
	 * @param mixed $buildName
	 * @return StyleSheet
	 */
	public function setBuildName($buildName)
	{
		$this->buildName = $buildName;
		return $this;
	}

	protected function makeContents()
	{
		return $this->doiuse->run(THEMEVIZ_BASE_PATH . "/build/$this->buildName/theme.css");
	}

	protected function getOutPath()
	{
		return "build/$this->buildName/cssAnalysis.txt";
	}
}