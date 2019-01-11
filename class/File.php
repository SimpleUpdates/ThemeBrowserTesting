<?php

namespace ThemeViz;

abstract class File
{
	/** @var Filesystem $filesystem */
	protected $filesystem;

	public function __construct(Filesystem $filesystem)
	{
		$this->filesystem = $filesystem;
	}

	public function save() {
		$this->filesystem->fileForceContents(
			THEMEVIZ_BASE_PATH . $this->getOutPath(),
			$this->makeContents()
		);
	}

	abstract protected function makeContents();
	abstract protected function getOutPath();
}