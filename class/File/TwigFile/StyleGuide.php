<?php

namespace ThemeViz\File\TwigFile;


use ThemeViz\ComponentFactory;
use ThemeViz\DataFactory;
use ThemeViz\File\TwigFile;
use ThemeViz\Filesystem;
use ThemeViz\Less;
use ThemeViz\Twig;

class StyleGuide extends TwigFile
{
	/** @var ComponentFactory $componentFactory */
	private $componentFactory;

	protected $template = "styleGuide.twig";
	protected $stylesheet = "styleGuide.less";

	public function __construct(ComponentFactory $componentFactory, DataFactory $dataFactory, Filesystem $filesystem, Less $less, Twig $twig)
	{
		parent::__construct($dataFactory, $filesystem, $less, $twig);

		$this->componentFactory = $componentFactory;
	}

	protected function getDataArray() {
		return [
			"themeviz_components" => $this->componentFactory->getComponents()
		];
	}

	protected function getBuildPath()
	{
		return "styleGuide.html";
	}
}