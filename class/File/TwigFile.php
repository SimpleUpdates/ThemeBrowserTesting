<?php

namespace ThemeViz\File;


use ThemeViz\DataFactory;
use ThemeViz\File;
use ThemeViz\Filesystem;
use ThemeViz\Less;
use ThemeViz\Twig;

abstract class TwigFile extends File
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

	/**
	 * @return string
	 * @throws \Exception
	 */
	protected function makeContents()
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

		return $this->twig->renderFile($this->template, $dataArray);
	}

	/**
	 * @return string
	 */
	protected function getOutPath(): string
	{
		return "/build/$this->buildPath";
	}

	abstract protected function getDataArray();
}