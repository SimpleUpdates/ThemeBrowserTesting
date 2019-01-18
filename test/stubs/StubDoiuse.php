<?php

namespace ThemeViz;


class StubDoiuse extends Doiuse
{
	use Stub;

	public function run($cssPath)
	{
		return $this->handleCall(__FUNCTION__, func_get_args());
	}
}