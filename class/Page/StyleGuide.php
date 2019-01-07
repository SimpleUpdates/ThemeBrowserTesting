<?php

namespace ThemeViz\Page;


use ThemeViz\Page;

class StyleGuide extends Page
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
}