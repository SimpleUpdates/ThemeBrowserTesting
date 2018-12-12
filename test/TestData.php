<?php

final class TestData extends ThemeViz\TestCase
{
	public function testReturnsStoredValues()
	{
		$s = new ThemeViz\Data([
			"getIcon" => "icon"
		]);

		$result = $s->getIcon();

		$this->assertEquals("icon", $result);
	}

	public function testTrainWithReturnValue()
	{
		$s = new ThemeViz\Data([
			"content" => "content"
		]);

		$result = $s->su()->content();

		$this->assertEquals("content", $result);
	}

	public function testVariableTrains()
	{
		$s = new ThemeViz\Data([
			"collection..find" => "result"
		]);

		$result = $s->su()->collection()->randomize()->find();

		$this->assertEquals("result", $result);
	}
}