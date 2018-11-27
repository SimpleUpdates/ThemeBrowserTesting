<?php

final class TestTwigCompiler extends ThemeViz\TestCase
{
    /** @var \ThemeViz\TwigCompiler $twigCompiler */
    private $twigCompiler;

    protected function setUp()
    {
        parent::setUp();

        $this->twigCompiler = $this->factory->getTwigCompiler();
    }

    public function testGetIcon()
    {
        $result = $this->twigCompiler->getIcon("icon-name");

        $this->assertContains("icon-name", $result);
    }

    public function testRegistersGetIconFunction()
    {
        $this->twigCompiler->compileTwig([],[]);

        $this->assertTrue($this->mockTwig->wasMethodCalledWith(
            "registerFunction",
            "getIcon",
            [$this->twigCompiler, "getIcon"],
            ["is_safe" => ["html"]]
        ));
    }
}