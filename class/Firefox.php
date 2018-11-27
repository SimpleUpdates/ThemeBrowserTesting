<?php

namespace ThemeViz;

class Firefox
{
    public function saveShot($dir, $filename, $sourcePath)
    {
        system("cd $dir; /usr/bin/firefox -screenshot $filename $sourcePath");
    }
}