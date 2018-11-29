<?php

namespace ThemeViz;


class SummaryCompiler
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Twig $twig */
    private $twig;

    public function __construct(Filesystem $filesystem, Twig $twig)
    {
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

        $html = $this->twig->renderFile("summary.twig", ["themeviz_components" => $components]);

        $this->filesystem->fileForceContents(
            THEMEVIZ_BASE_PATH . "/build/summary.html",
            $html
        );
    }
}