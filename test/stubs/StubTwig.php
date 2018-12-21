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
}