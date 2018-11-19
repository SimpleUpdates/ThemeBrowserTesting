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
        $file = array_pop($parts);
        $dir = '';
        foreach ($parts as $part) {
            if (!is_dir($dir .= "/$part")) mkdir($dir);
        }
        file_put_contents("$dir/$file", $contents);
    }
}