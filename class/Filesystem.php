<?php

namespace ThemeViz;

class Filesystem
{
    public function getFile($path)
    {
        return file_get_contents($path);
    }

    public function deleteTree($dir)
    {
        return system("rm -r $dir/*");
    }

    public function fileForceContents($path, $contents)
    {
        $parts = explode('/', $path);
        array_pop($parts);
        $dir = implode("/", $parts);
        $this->makeTree($dir);
        file_put_contents($path, $contents);
    }

    /**
     * @param $path
     */
    public function makeTree($path)
    {
        $parts = explode("/", $path);
        $dir = "";
        foreach ($parts as $part) {
            if (!is_dir($dir .= "/$part")) $this->makeDir($dir);
        }
    }

    public function makeDir($path)
    {
        mkdir($path);
    }
}