<?php

namespace ThemeViz;

class StubPixelmatch extends Pixelmatch
{
    use Stub;

    public function makeDiff($image1Path, $image2Path, $outputPath, $threshold=0.005, $includeAA=false)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }
}