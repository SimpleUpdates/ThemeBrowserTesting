<?php

namespace ThemeViz;

class StubLess extends Less
{
    use Stub;

    public function parse(string $less): void
    {
        $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function parseFile(string $filename, string $uriRoot): void
    {
        $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function getCss()
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }
}