<?php

namespace ThemeViz;

class StubFirefox extends Firefox
{
    use Stub;

    public function saveShot($dir, $filename, $sourcePath)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }
}