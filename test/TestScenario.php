<?php

final class TestScenario extends ThemeViz\TestCase
{
	public function testReturnsStoredValues()
	{
		$s = new ThemeViz\Scenario([
			"getIcon" => "icon"
		]);

		$result = $s->getIcon();

		$this->assertEquals("icon", $result);
	}

	public function testTrainWithReturnValue()
	{
		$s = new ThemeViz\Scenario([
			"content" => "content"
		]);

		$result = $s->su()->content();

		$this->assertEquals("content", $result);
	}

	public function testVariableTrains()
	{
		$s = new ThemeViz\Scenario([
			"collection..find" => "result"
		]);

		$result = $s->su()->collection()->randomize()->find();

		$this->assertEquals("result", $result);
	}
}