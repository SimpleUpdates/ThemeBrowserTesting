<?php

namespace ThemeViz;

class StubTwig extends Twig
{
    use Stub;

    public function renderFile($templateFile, $data = [], $state = "head")
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function registerFunction($name, $function, $options = [])
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

	public function assertTwigTemplateRendered($template, $data = [], $state = null)
	{
		$this->assertTwigTemplateRenderedWithDataMatching($template, $state, [$this, "doesDataIncludeData"], $data);
	}

	public function assertTwigTemplateRenderedWithDataMatching($template, $state, $callable, ...$params)
	{
		$this->assertAnyCallMatches("renderFile", function($carry, $call) use($template, $state, $callable, $params) {
			$callTemplate = $call[0];
			$callData = $call[1];
			$callState = $call[2];

			$doesTemplateMatch = $callTemplate === $template;
			$doesStateMatch = $state ? $callState === $state : true;
			$doesCallableMatch = call_user_func($callable, $callData, ...$params);

			return $carry || ($doesTemplateMatch && $doesStateMatch && $doesCallableMatch);
		});
	}

	private function doesDataIncludeData($haystack, $keyValueNeedles)
	{
		return empty( array_diff( $keyValueNeedles, $haystack ) );
	}
}