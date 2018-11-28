<?php

namespace ThemeViz;

class Photographer
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Firefox $firefox */
    private $firefox;

    public function __construct(
        Filesystem $filesystem,
        Firefox $firefox
    )
    {
        $this->filesystem = $filesystem;
        $this->firefox = $firefox;
    }

    public function photographComponents($componentFolder, $photoFolder)
    {
        $scanDir = $this->filesystem->scanDir($componentFolder) ?? [];
        $components = array_diff($scanDir, ["..","."]);
        $this->filesystem->makeTree($photoFolder);

        array_map(function ($component) use ($componentFolder, $photoFolder) {
            $basename = explode(".", $component)[0];
            $photoFilename = "$basename.png";

            $componentPath = "$componentFolder/$component";

            $isDir = $this->filesystem->isDir($componentPath);

            if (!$isDir) {
                $this->firefox->saveShot(
                    $photoFolder,
                    $photoFilename,
                    $componentPath
                );
            } else {
                $this->photographComponents($componentPath, $photoFolder);
            }
        }, $components);
    }
}