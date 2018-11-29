<?php

namespace ThemeViz;

class Pixelmatch
{
    public function makeDiff($image1Path, $image2Path, $outputPath, $threshold=0.005, $includeAA=false)
    {
        $c = "pixelmatch $image1Path $image2Path $outputPath threshold=$threshold includeAA=$includeAA";

        $this->exec($c);
    }

    private function exec($command)
    {
        exec($command, $output);

        return $output;
    }
}