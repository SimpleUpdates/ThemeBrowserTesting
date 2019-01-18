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
	 * @return CssAnalysis
	 */
	public function setBuildName($buildName)
	{
		$this->buildName = $buildName;
		return $this;
	}

	protected function makeContents()
	{
		$text = $this->doiuse->run(THEMEVIZ_BASE_PATH . "/build/$this->buildName/theme.css");

		return nl2br($text);
	}

	protected function getOutPath()
	{
		return "build/$this->buildName/cssAnalysis.html";
	}
}