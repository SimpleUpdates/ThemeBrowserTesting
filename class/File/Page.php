<?php

namespace ThemeViz\File;


use ThemeViz\DataFactory;
use ThemeViz\File;
use ThemeViz\Filesystem;
use ThemeViz\Less;
use ThemeViz\Twig;

abstract class Page extends File
{
	/** @var DataFactory $dataFactory */
	protected $dataFactory;

	/** @var Filesystem $filesystem */
	protected $filesystem;

	/** @var Less */
	protected $less;

	/** @var Twig $twig */
	protected $twig;

	protected $template;
	protected $stylesheet;
	protected $buildPath;

	public function __construct(DataFactory $dataFactory, Filesystem $filesystem, Less $less, Twig $twig)
	{
		$this->dataFactory = $dataFactory;
		$this->filesystem = $filesystem;
		$this->less = $less;
		$this->twig = $twig;
	}

	public function compile()
	{
		$dataArray = $this->getDataArray();
		$data = $this->dataFactory->makeData($dataArray);

		if ($this->stylesheet) {
			$styleFolder = THEMEVIZ_BASE_PATH . "/style";
			$filePath = "$styleFolder/$this->stylesheet";
			$this->less->parseFile($filePath, $styleFolder);
			$css = $this->less->getCss();
			$dataArray["themeviz_css"] = $css;
		}

		array_merge([
			"themeviz_theme_path" => THEMEVIZ_THEME_PATH
		], $dataArray);

		$html = $this->twig->renderFile($this->template, $dataArray);

		$this->filesystem->fileForceContents(
			THEMEVIZ_BASE_PATH . "/build/$this->buildPath",
			$html
		);
	}

	abstract protected function getDataArray();
}