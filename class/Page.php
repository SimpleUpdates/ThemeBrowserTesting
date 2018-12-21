<?php

namespace ThemeViz;


abstract class Page
{
	/** @var DataFactory $dataFactory */
	private $dataFactory;

	/** @var Filesystem $filesystem */
	private $filesystem;

	/** @var Twig $twig */
	private $twig;

	protected $template;
	protected $buildPath;

	public function __construct(DataFactory $dataFactory, Filesystem $filesystem, Twig $twig)
	{
		$this->dataFactory = $dataFactory;
		$this->filesystem = $filesystem;
		$this->twig = $twig;
	}

	public function compile()
	{
		$dataArray = $this->getDataArray();
		$data = $this->dataFactory->makeData($dataArray);
		$html = $this->twig->renderFile($this->template, $dataArray);

		$this->filesystem->fileForceContents(
			THEMEVIZ_BASE_PATH . "/build/$this->buildPath",
			$html
		);
	}

	abstract protected function getDataArray();
}