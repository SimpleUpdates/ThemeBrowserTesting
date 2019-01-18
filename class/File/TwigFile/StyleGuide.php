<?php

namespace ThemeViz\File\TwigFile;


use ThemeViz\File\TwigFile;

class StyleGuide extends TwigFile
{
	protected $template = "styleGuide.twig";
	protected $stylesheet = "styleGuide.less";
 	protected $buildPath = "styleGuide.html";

	protected function getDataArray() {
		$paths = $this->filesystem->findPathsMatchingRecursive(
			THEMEVIZ_BASE_PATH . "/build/head/html",
			"/\.html$/"
		);

		$components = array_map(function($path) {
			$relativePath = explode("/build/", $path)[1];

			return [
				"name" => basename($path),
				"html" => $relativePath,
			];
		}, $paths ?? []);

		return [
			"themeviz_components" => $components
		];
	}

	protected function getBuildPath()
	{
		// TODO: Implement getBuildPath() method.
	}
}