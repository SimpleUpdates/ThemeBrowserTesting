<?php

namespace ThemeViz;

class Backstop
{
    public function test(string $configPath)
    {
        exec(THEMEVIZ_BASE_PATH . "/node_modules/backstopjs/cli/index.js test --config=$configPath");
    }
}