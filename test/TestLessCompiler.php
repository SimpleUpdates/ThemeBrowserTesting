<?php

final class TestLessCompiler extends ThemeViz\TestCase
{
    /** @var \ThemeViz\LessCompiler $lessCompiler */
    private $lessCompiler;

    protected function setUp()
    {
        parent::setUp();

        $this->lessCompiler = $this->factory->getLessCompiler();
    }

    /**
     * @throws Less_Exception_Parser
     */
    public function testDoesNotCacheCss()
    {
        $this->mockLess->setReturnValue("getCss", "compiled_css");

        $this->lessCompiler->getCss([],[]);
        $this->lessCompiler->getCss([],[]);

        $this->mockLess->assertCallCount("getCss", 2);
    }

    /**
     * @throws Less_Exception_Parser
     */
    public function testResetsParser()
    {
        $this->lessCompiler->getCss([],[]);

        $this->mockLess->assertMethodCalled("resetParser");
    }
}