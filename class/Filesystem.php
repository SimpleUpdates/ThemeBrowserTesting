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
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
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