<?php
namespace ThemeViz;


class Scenario
{
	private $data;
	private $matchers;
	private $train = [];

	public function __construct($data = [])
	{
		$this->data = $data;
		$this->matchers = $this->makeMatchers($this->data);
	}

	public function __call($name, $arguments)
	{
		$this->train[] = $name;

		return $this->getReturnValue() ?? $this;
	}

	private function makeMatchers(array $data)
	{
		$keys = array_keys($data);

		$keyMatchers = array_reduce($keys, function($carry, $key) {
			$matcherEscaped = str_replace(".", "\.", $key);
			$matcherExpanded = str_replace("\.\.", "\..+\.", $matcherEscaped);
			$matcherComplete = "#$matcherExpanded#";

			return array_merge($carry, [ $key => $matcherComplete ]);
		}, []);

		return array_reduce($keys, function($carry, $key) use($keyMatchers, $data) {
			$matcher = $keyMatchers[$key];
			$value = $data[$key];

			return array_merge($carry, [$matcher => $value]);
		}, []);
	}

	/**
	 * @return mixed
	 */
	private function getReturnValue()
	{
		$trainString = implode(".", $this->train);

		return array_reduce(array_keys($this->matchers), function ($carry, $key) use ($trainString) {
			$regex = $key;
			$value = $this->matchers[$key];

			return (preg_match($regex, $trainString) === 1) ? $value : $carry;
		});
	}
}