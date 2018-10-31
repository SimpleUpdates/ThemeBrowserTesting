<?php

namespace ThemeViz;

class StubTwig extends Twig
{
    use Stub;

    public function render($templateFile, $data = [])
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function registerFunction($name, $function, $options = [])
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }
}