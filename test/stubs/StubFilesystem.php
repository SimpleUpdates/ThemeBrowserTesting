<?php

namespace ThemeViz;

class StubFilesystem extends Filesystem
{
    use Stub;

    public function getFile($path)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function deleteTree($dir)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function fileForceContents($path, $contents)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function makeTree($path)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }
}