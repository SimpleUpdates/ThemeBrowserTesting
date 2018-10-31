#!/usr/bin/php
<?php

namespace ThemeViz;

define("THEMEVIZ_BASE_PATH", dirname(__FILE__));
include_once(THEMEVIZ_BASE_PATH . "/vendor/autoload.php");

$providedPath = $argv[1] ?? readline("Theme Path: ");

define("THEMEVIZ_THEME_PATH", realpath($providedPath));

var_dump(THEMEVIZ_THEME_PATH);

echo THEMEVIZ_THEME_PATH . "\r\n";

if (!THEMEVIZ_THEME_PATH) {
    echo "Failed to find theme path\r\n";
    exit;
}

$factory = new Factory();
$renderer = $factory->getRenderer();

$renderer->compile();