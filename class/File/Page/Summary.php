<?php

namespace ThemeViz\File\Page;


use ThemeViz\File\Page;

class Summary extends Page
{
    protected $template = "summary.twig";
    protected $buildPath = "summary.html";

    protected function getDataArray()
    {
        $files = $this->filesystem->scanDir(THEMEVIZ_BASE_PATH . "/build/diffs");

        $components = array_map(function($filename) {
            return [
                "name" => $filename,
                "expected" => "production/shots/$filename",
                "actual" => "head/shots/$filename",
                "diff" => "diffs/$filename"
            ];
        }, $files ?? []);

		return ["themeviz_components" => $components];
    }
}