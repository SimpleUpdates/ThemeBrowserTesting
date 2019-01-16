<?php

namespace ThemeViz;

class BuildFactory {
	/** @var Factory $factory */
	private $factory;

	public function __construct(Factory $factory)
	{
		$this->factory = $factory;
	}

	public function makeBuild($name)
	{
		/** @var Build $build */
		$build = $this->factory->make("Build");
		$build->setName($name);

		return $build;
	}
}