<?php

namespace ThemeViz\Page;


use ThemeViz\DataFactory;
use ThemeViz\Filesystem;
use ThemeViz\Page;
use ThemeViz\Twig;

class Summary extends Page
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    protected $template = "summary.twig";
    protected $buildPath = "summary.html";

    public function __construct(DataFactory $dataFactory, Filesystem $filesystem, Twig $twig)
    {
    	parent::__construct($dataFactory, $filesystem, $twig);

        $this->filesystem = $filesystem;
    }

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