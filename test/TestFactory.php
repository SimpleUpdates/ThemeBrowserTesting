<?php

final class TestFactory extends \ThemeViz\TestCase
{
    public function testExists()
    {
        $f = new \ThemeViz\Factory();

        $this->assertInstanceOf("\\ThemeViz\\Factory", $f);
    }
}