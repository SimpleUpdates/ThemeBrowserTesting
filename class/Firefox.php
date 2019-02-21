<?php

namespace ThemeViz;

class Firefox
{
    public function saveShot($dir, $filename, $sourcePath)
    {
		$command = "cd $dir; /usr/bin/firefox -screenshot $filename $sourcePath";
		echo $command . PHP_EOL;
		system($command);
    }
}