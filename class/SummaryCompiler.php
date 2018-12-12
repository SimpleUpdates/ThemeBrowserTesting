<?php

namespace ThemeViz;


class SummaryCompiler
{
	/** @var DataFactory $dataFactory */
	private $dataFactory;

    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Twig $twig */
    private $twig;

    public function __construct(DataFactory $dataFactory, Filesystem $filesystem, Twig $twig)
    {
    	$this->dataFactory = $dataFactory;
        $this->filesystem = $filesystem;
        $this->twig = $twig;
    }

    public function compile()
    {
        $files = $this->filesystem->scanDir(THEMEVIZ_BASE_PATH . "/build/diffs");

        $components = array_map(function($filename) {
            return [
                "name" => $filename,
                "expected" => "production/shots/$filename",
                "actual" => "pull/shots/$filename",
                "diff" => "diffs/$filename"
            ];
        }, $files ?? []);

        $data = $this->dataFactory->makeData(["themeviz_components" => $components]);

        $html = $this->twig->renderFile("summary.twig", $data);

        $this->filesystem->fileForceContents(
            THEMEVIZ_BASE_PATH . "/build/summary.html",
            $html
        );
    }
}