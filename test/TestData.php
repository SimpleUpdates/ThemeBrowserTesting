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

	public function testCollectionReturnValues()
	{
		$s = new ThemeViz\Data([], [
			"myCollection" => "result"
		]);

		$result = $s->su()->collection("myCollection")->randomize()->find();

		$this->assertEquals("result", $result);
	}

	public function testCollectionReturnsEmptyArrayIfNotInstantiated()
	{
		$s = new ThemeViz\Data([], []);

		$result = $s->su()->collection("myCollection")->randomize()->find();

		$this->assertEquals([], $result);
	}

	public function testVariableTrains()
	{
		$s = new ThemeViz\Data([
			"one..three" => "result"
		]);

		$result = $s->zero()->one()->two()->three();

		$this->assertEquals("result", $result);
	}

	public function testAddData()
	{
		$s = new ThemeViz\Data();

		$s->addData(["new" => "data"]);

		$result = $s->new();

		$this->assertEquals("data", $result);
	}
}