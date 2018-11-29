<?php

namespace ThemeViz;


class Differ
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Pixelmatch $pixelmatch */
    private $pixelmatch;

    public function __construct(
        Filesystem $filesystem,
        Pixelmatch $pixelmatch
    )
    {
        $this->filesystem = $filesystem;
        $this->pixelmatch = $pixelmatch;
    }

    public function buildDiffs()
    {
        $this->filesystem->makeTree(THEMEVIZ_BASE_PATH . "/build/diffs");
        $productionShots = $this->filesystem->scanDir(THEMEVIZ_BASE_PATH . "/build/production/shots");
        $pullShots = $this->filesystem->scanDir(THEMEVIZ_BASE_PATH . "/build/pull/shots");
        $shotsToDiff = array_intersect($productionShots ?? [], $pullShots ?? []);

        array_map(function ($shot) {
            $this->pixelmatch->makeDiff(
                THEMEVIZ_BASE_PATH . "/build/production/shots/$shot",
                THEMEVIZ_BASE_PATH . "/build/pull/shots/$shot",
                THEMEVIZ_BASE_PATH . "/build/diffs/$shot"
            );
        }, $shotsToDiff);
    }
}