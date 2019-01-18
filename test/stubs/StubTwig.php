<?php

namespace ThemeViz;

class StubTwig extends Twig
{
    use Stub;

    public function renderFile($templateFile, $data = [])
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function registerFunction($name, $function, $options = [])
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

	public function assertTwigTemplateRendered($template)
	{
		$message = "Failed to assert that $template was rendered";
		$this->assertAnyCallMatches("renderFile", function($carry, $call) use($template) {
			$callTemplate = $call[0];
			return $carry || ($callTemplate === $template);
		}, $message);
	}

	public function assertTwigTemplateRenderedWithData($template, $data)
	{
		$message = "Failed to assert that $template was rendered with data";
		$this->assertAnyCallMatches("renderFile", function($carry, $call) use($template, $data) {
			$callTemplate = $call[0];
			$callData = $call[1];

			$withTemplate = $callTemplate === $template;
			$withData = empty( array_diff( $data, $callData ) );

			return $carry || ( $withTemplate && $withData ) ;
		}, $message);
	}
}