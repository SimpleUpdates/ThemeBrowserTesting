<?php

final class TestComponent extends ThemeViz\TestCase
{
	public function testExists()
	{
		$c = new ThemeViz\Component([],[],[],[],[]);

		$this->assertInstanceOf("\\ThemeViz\\Component", $c);
	}
}