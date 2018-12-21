<?php

namespace ThemeViz\Page;


use ThemeViz\DataFactory;
use ThemeViz\Filesystem;
use ThemeViz\Page;
use ThemeViz\Twig;

class StyleGuide extends Page
{
	/** @var Filesystem $filesystem */
	private $filesystem;

	protected $template = "styleGuide.twig";
	protected $buildPath = "styleGuide.html";

	public function __construct(DataFactory $dataFactory, Filesystem $filesystem, Twig $twig)
	{
		parent::__construct($dataFactory, $filesystem, $twig);

		$this->filesystem = $filesystem;
	}

	protected function getDataArray() {
		$paths = $this->filesystem->findPathsMatchingRecursive(
			THEMEVIZ_BASE_PATH . "/build/head/html",
			"/\.html$/"
		);

		$components = array_map(function($path) {
			return [
				"name" => basename($path),
				"html" => $path,
			];
		}, $paths ?? []);

		return ["themeviz_components" => $components];
	}
}